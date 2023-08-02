<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Models\PendingMedia;
use Modules\Core\OAuth\EmptyRefreshTokenException;
use Modules\Core\Resource\AssociatesResources;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\MailClient\Client\Compose\AbstractComposer;
use Modules\MailClient\Client\Compose\Message;
use Modules\MailClient\Client\Compose\MessageForward;
use Modules\MailClient\Client\Compose\MessageReply;
use Modules\MailClient\Client\Exceptions\ConnectionErrorException;
use Modules\MailClient\Client\Exceptions\FolderNotFoundException;
use Modules\MailClient\Client\Exceptions\MessageNotFoundException;
use Modules\MailClient\Concerns\InteractsWithEmailMessageAssociations;
use Modules\MailClient\Criteria\EmailAccountMessageCriteria;
use Modules\MailClient\Http\Requests\MessageRequest;
use Modules\MailClient\Http\Resources\EmailAccountMessageResource;
use Modules\MailClient\Models\EmailAccount;
use Modules\MailClient\Models\EmailAccountMessage;
use Modules\MailClient\Services\EmailAccountMessageSyncService;

class EmailAccountMessagesController extends ApiController
{
    use InteractsWithEmailMessageAssociations,
        AssociatesResources;

    /**
     * Get messages for account folder
     *
     * @param  int  $accountId
     * @param  int  $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($accountId, $folderId, Request $request)
    {
        $this->authorize('view', EmailAccount::findOrFail($accountId));

        $messages = EmailAccountMessage::withCommon()
            ->criteria(new EmailAccountMessageCriteria($accountId, $folderId))
            ->paginate($request->integer('per_page', null));

        return $this->response(
            EmailAccountMessageResource::collection($messages)
        );
    }

    /**
     * Send new message
     *
     * @param  int  $accountId
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($accountId, MessageRequest $request)
    {
        $this->authorize('view', $account = EmailAccount::findOrFail($accountId));

        $composer = new Message(
            $account->createClient(),
            $account->sentFolder->identifier()
        );

        return $this->sendMessage($composer, $accountId, $request);
    }

    /**
     * Reply to a message
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reply($id, MessageRequest $request)
    {
        $message = EmailAccountMessage::with(['account', 'folders.account'])->findOrFail($id);

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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forward($id, MessageRequest $request)
    {
        $message = EmailAccountMessage::with(['account', 'folders.account'])->findOrFail($id);

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
                $attachment->filename.'.'.$attachment->extension
            );
        }

        return $this->sendMessage($composer, $message->email_account_id, $request);
    }

    /**
     * Get email account message
     *
     * @param  int  $folderId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($folderId, $id)
    {
        $message = EmailAccountMessage::withCommon()->findOrFail($id);

        $this->authorize('view', $message->account);

        try {
            $message->markAsRead($folderId);
        } catch (MessageNotFoundException $e) {
            return $this->response(['message' => 'The message does not exist on remote server.'], 409);
        } catch (FolderNotFoundException $e) {
            return $this->response(['message' => 'The folder the message belongs to does not exist on remote server.'], 409);
        } catch (EmptyRefreshTokenException) {
            // Probably here the account is disabled and no other actions are needed
        }

        // Reload the account and all it's relationship so the unread_count of the folders
        // is updated  in case the message was marked as read above.
        $message->load(['account' => function ($query) {
            $query->withCommon();
        }]);
        return view('admin.pages.emails.view-email',compact('message'))->render();

        return $this->response((new EmailAccountMessageResource($message))->withActions(
            $message::resource()->resolveActions(
                app(ResourceRequest::class)->setResource($message::resource()->name())
            )
        ));
    }

    /**
     * Delete message from storage
     *
     * @param  int  $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($messageId, EmailAccountMessageSyncService $service)
    {
        $message = EmailAccountMessage::findOrFail($messageId);

        $this->authorize('view', $message->account);

        $service->delete($message->id);

        return $this->response('', 204);
    }

    /**
     * Mark the given message as read
     *
     * @param  int  $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function read($messageId)
    {
        $message = EmailAccountMessage::withCommon()->find($messageId);

        $message->markAsRead();

        return $this->response(new EmailAccountMessageResource(
            $message
        ));
    }

    /**
     * Mark the given message as unread
     *
     * @param  int  $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function unread($messageId)
    {
        $message = EmailAccountMessage::withCommon()->find($messageId);

        $message->markAsUnread();

        return $this->response(new EmailAccountMessageResource(
            $message
        ));
    }

    /**
     * Send the message
     *
     * @param  \Modules\MailClient\Client\Compose\AbstractComposer  $message
     * @param  int  $acountId
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendMessage(AbstractComposer $composer, $accountId, Request $request)
    {
        $this->addComposerAssociationsHeaders($composer, $request->input('associations', []));
        $this->addPendingAttachments($composer, $request);

        try {
            $composer->subject($request->subject)
                ->to($request->to)
                ->bcc($request->bcc)
                ->cc($request->cc)
                ->htmlBody($request->message)
                ->withTrackers();

            $message = $composer->send();
        } catch (ConnectionErrorException $e) {
          return $this->response(['message' => "A connection error occured, re-authenticate or try again later.{$e->getMessage()}"], 409);
        } catch (MessageNotFoundException) {
            return $this->response(['message' => 'The message does not exist on remote server.'], 409);
        } catch (FolderNotFoundException) {
            return $this->response(['message' => 'The folder the message belongs to does not exist on remote server.'], 409);
        } catch (\Exception $e) {
            return $this->response(['message' => $e->getMessage()], 500);
        }

        if (! is_null($message)) {
            $dbMessage = (new EmailAccountMessageSyncService())->create(
                $accountId,
                $message,
                $this->filterAssociations('emails', $request->associations)
            );

            $jsonResource = new EmailAccountMessageResource(
                EmailAccountMessage::withCommon()->find($dbMessage->id)
            );

            return $this->response([
                'message' => $jsonResource->withActions(
                    $dbMessage::resource()->resolveActions(
                        app(ResourceRequest::class)->setResource($dbMessage::resource()->name())
                    )
                ),
                'createdActivity' => null,
            ], 201);
        }

        return $this->response([
            'createdActivity' => null,
        ], 202);
    }

    /**
     * Add the attachments (if any) to the message composer.
     */
    protected function addPendingAttachments(AbstractComposer $composer, Request $request): void
    {
        if ($request->attachments_draft_id) {
            $attachments = PendingMedia::with('attachment')->ofDraftId($request->attachments_draft_id)->get();

            foreach ($attachments as $pendingMedia) {
                $composer->attachFromStorageDisk(
                    $pendingMedia->attachment->disk,
                    $pendingMedia->attachment->getDiskPath(),
                    $pendingMedia->attachment->filename.'.'.$pendingMedia->attachment->extension
                );
            }
        }
    }
}
