<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Http\Controllers\Admin\EmailAccount;

use Illuminate\Http\Request;
use App\MailClient\MailTracker;
use App\Http\Requests\MessageRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Innoclapps\MailClient\Compose\Message;
use App\Innoclapps\Resources\AssociatesResources;
use App\Innoclapps\Resources\Http\ResourceRequest;
use App\Http\Resources\EmailAccountMessageResource;
use App\Innoclapps\MailClient\Compose\MessageReply;
use App\Innoclapps\OAuth\EmptyRefreshTokenException;
use App\Innoclapps\MailClient\Compose\MessageForward;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Innoclapps\MailClient\Compose\AbstractComposer;
use App\Criteria\EmailAccount\EmailAccountMessageCriteria;
use App\Contracts\Repositories\EmailAccountMessageRepository;
use App\Support\Concerns\InteractsWithEmailMessageAssociations;
use App\Innoclapps\Contracts\Repositories\PendingMediaRepository;
use App\Innoclapps\MailClient\Exceptions\FolderNotFoundException;
use App\Innoclapps\MailClient\Exceptions\MessageNotFoundException;

class EmailAccountMessagesController extends Controller
{
    use InteractsWithEmailMessageAssociations,
        AssociatesResources;

    /**
    * Initialize new EmailAccountMessagesController instance.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $accounts
     * @param \App\Contracts\Repositories\EmailAccountMessageRepository $messages
     * @param \App\Innoclapps\Contracts\Repositories\PendingMediaRepository $media
     */
    public function __construct(
        protected EmailAccountRepository $accounts,
        protected EmailAccountMessageRepository $messages,
        protected PendingMediaRepository $media,
    ) {
    }

    /**
     * Get messages for account folder
     *
     * @param int $accountId
     * @param int $folderId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($accountId, $folderId, Request $request)
    {
        $this->authorize('view', $this->accounts->find($accountId));

        $messages = $this->messages->withResponseRelations()
            ->pushCriteria(new EmailAccountMessageCriteria($accountId, $folderId))
            ->paginate($request->integer('per_page', null));

        return $this->response(
            EmailAccountMessageResource::collection($messages)
        );
    }

    /**
     * Send new message
     *
     * @param int $accountId
     * @param \App\Http\Requests\MessageRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($accountId, MessageRequest $request)
    {
        $this->authorize('view', $account = $this->accounts->find($accountId));

        $composer = new Message(
            $account->createClient(),
            $account->sentFolder->identifier()
        );

        return $this->sendMessage($composer, $accountId, $request);
    }

    /**
     * Reply to a message
     *
     * @param int $id
     * @param \App\Http\Requests\MessageRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reply($id, MessageRequest $request)
    {
        $message = $this->messages->with(['account', 'folders.account'])->find($id);

        $this->authorize('view', $message->account);

        $composer = new MessageReply(
            $message->account->createClient(),
            $message->remote_id,
            $message->folders->first()->identifier(),
            $message->account->sentFolder->identifier()
        );

        return $this->sendMessage($composer, $message->email_account_id, $request);
    }

    /**
     * Forward a message
     *
     * @param int $id
     * @param \App\Http\Requests\MessageRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forward($id, MessageRequest $request)
    {
        $message = $this->messages->with(['account', 'folders.account'])->find($id);

        $this->authorize('view', $message->account);

        $composer = new MessageForward(
            $message->account->createClient(),
            $message->remote_id,
            $message->folders->first()->identifier(),
            $message->account->sentFolder->identifier()
        );

        // Add the original selected message attachments
        foreach ($message->attachments->find($request->forward_attachments ?? []) as $attachment) {
            $composer->attachFromStorageDisk(
                $attachment->disk,
                $attachment->getDiskPath(),
                $attachment->filename . '.' . $attachment->extension
            );
        }

        return $this->sendMessage($composer, $message->email_account_id, $request);
    }

    /**
     * Get email account message
     *
     * @param int $folderId
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($folderId, $id)
    {
        $message = $this->messages->find($id);

        $this->authorize('view', $message->account);

        try {
            $this->messages->markAsRead($message->id, $folderId);
        } catch (MessageNotFoundException $e) {
            return $this->response(['message' => 'The message does not exist on remote server.'], 409);
        } catch (FolderNotFoundException $e) {
            return $this->response(['message' => 'The folder the message belongs to does not exist on remote server.'], 409);
        } catch (EmptyRefreshTokenException $e) {
            // Probably here the account is disabled and no other actions are needed
        }

        // Load relations after marked as read so the folders unread_count is correct
        $message->loadMissing($this->messages->getResponseRelations());

        return $this->response((new EmailAccountMessageResource($message))->withActions(
            $message::resource()->resolveActions(
                app(ResourceRequest::class)->setResource($message::resource()->name())
            )
        ));
    }

    /**
     * Delete message from storage
     *
     * @param int $messageId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($messageId)
    {
        $message = $this->messages->find($messageId);

        $this->authorize('view', $message->account);

        $this->messages->deleteForAccount($message->id);

        return $this->response('', 204);
    }

    /**
     * Mark the given message as read
     *
     * @param int $messageId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read($messageId)
    {
        $this->messages->markAsRead($messageId);

        return $this->response(new EmailAccountMessageResource(
            $this->messages->withResponseRelations()->find($messageId)
        ));
    }

    /**
     * Mark the given message as unread
     *
     * @param int $messageId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unread($messageId)
    {
        $this->messages->markAsUnread($messageId);

        return $this->response(new EmailAccountMessageResource(
            $this->messages->withResponseRelations()->find($messageId)
        ));
    }

    /**
     * Send the message
     *
     * @param \App\Innoclapps\MailClient\Compose\AbstractComposer $message
     * @param int $acountId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendMessage(AbstractComposer $composer, $accountId, Request $request)
    {
        // $this->addComposerAssociationsHeaders($composer, $request->input('associations', []));
        $this->addPendingAttachments($composer, $request);

        try {
            $composer->subject($request->subject)
                ->to($request->to)
                ->bcc($request->bcc)
                ->cc($request->cc)
                ->htmlBody($request->message);

            (new MailTracker)->createTrackers($composer);

            $message = $composer->send();
        } catch (MessageNotFoundException $e) {
            return $this->response(['message' => 'The message does not exist on remote server.'], 409);
        } catch (FolderNotFoundException $e) {
            return $this->response(['message' => 'The folder the message belongs to does not exist on remote server.'], 409);
        } catch (\Exception $e) {
            return $this->response(['message' => $e->getMessage()], 500);
        }

        if (! is_null($message)) {
            $dbMessage = $this->messages->createForAccount(
                $accountId,
                $message,
                $this->filterAssociations('emails', $request->associations)
            );

            $jsonResource = new EmailAccountMessageResource(
                $this->messages->withResponseRelations()->find($dbMessage->id)
            );

            return $this->response([
                'message' => $jsonResource->withActions(
                    $dbMessage::resource()->resolveActions(
                        app(ResourceRequest::class)->setResource($dbMessage::resource()->name())
                    )
                ),
                'createdActivity' => $task ? new ActivityResource($task) : null,
            ], 201);
        }

        return $this->response([
            'createdActivity' => $task ? new ActivityResource($task) : null,
        ], 202);
    }

    /**
     * Handle the follow up task creation, it's created here
     * because if the message is not sent immediately we won't be able
     * to return the activity
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null|\App\Models\Activity
     */


    /**
     * Add the attachments (if any) to the message composer
     *
     * @param \App\Innoclapps\MailClient\Compose\AbstractComposer $composer
     * @param \Iluminate\Http\Request $request
     *
     * @return void
     */
    protected function addPendingAttachments(AbstractComposer $composer, Request $request)
    {
        if ($request->attachments_draft_id) {
            $attachments = $this->media->getByDraftId($request->attachments_draft_id);

            foreach ($attachments as $pendingMedia) {
                $composer->attachFromStorageDisk(
                    $pendingMedia->attachment->disk,
                    $pendingMedia->attachment->getDiskPath(),
                    $pendingMedia->attachment->filename . '.' . $pendingMedia->attachment->extension
                );
            }
        }
    }
}
