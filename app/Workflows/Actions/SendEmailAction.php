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

namespace App\Workflows\Actions;

use App\Models\User;
use ReflectionClass;
use Illuminate\Support\Arr;
use App\Innoclapps\Fields\Text;
use App\MailClient\MailTracker;
use App\Innoclapps\Fields\Email;
use App\Innoclapps\Fields\Select;
use App\Innoclapps\Workflow\Action;
use App\Innoclapps\Fields\MailEditor;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Contracts\Presentable;
use App\Innoclapps\Placeholders\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Innoclapps\MailableTemplates\Renderer;
use App\Innoclapps\MailClient\Compose\Message;
use App\Innoclapps\Resources\MailPlaceholders;
use App\Innoclapps\Contracts\Resources\HasEmail;
use App\Contracts\Repositories\EmailAccountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Criteria\EmailAccount\EmailAccountsForUserCriteria;
use App\Contracts\Repositories\EmailAccountMessageRepository;
use App\Support\Concerns\InteractsWithEmailMessageAssociations;
use App\Innoclapps\MailClient\Exceptions\ConnectionErrorException;

class SendEmailAction extends Action implements ShouldQueue
{
    use InteractsWithEmailMessageAssociations;

    /**
     * Available mail template merge fields
     *
     * @var callable|null
     */
    public $placeholders;

    /**
     * Special resources To field
     *
     * @var null|\App\Workflows\Actions\ResourcesSendEmailToField
     */
    public $resourcesToField;

    /**
     * Fields provider callback
     *
     * @var callable|null
     */
    public $fieldsCallback;

    /**
     * Run the trigger
     *
     * @return void
     */
    public function run()
    {
        try {
            $account = $this->repository()->find($this->email_account_id);
        } catch (ModelNotFoundException $e) {
            return;
        }

        if (! $account->canSendMails()) {
            return;
        }

        try {
            if ($address = $this->getEmailAddress()) {
                $composer = new Message($account->createClient(), $account->sentFolder->identifier());

                $message = tap($composer, function ($instance) use ($address) {
                    $instance->htmlBody($this->parseMessageBodyPlaceholders())
                        ->subject($this->subject)
                        ->to($address);

                    (new MailTracker)->createTrackers($instance);

                    if ($resources = $this->getAssociateableResources()) {
                        $this->addComposerAssociationsHeaders($instance, $resources);
                    }
                })->send();

                if ($message) {
                    resolve(EmailAccountMessageRepository::class)->createForAccount(
                        $account->id,
                        $message,
                        $this->getAssociateableResources() ?? []
                    );
                }
            }
        } catch (ConnectionErrorException $e) {
            $this->repository()->setRequiresAuthentication((int) $account->id);
        }
    }

    /**
     * Parse message body placeholders for the current email
     *
     * @return string
     */
    protected function parseMessageBodyPlaceholders()
    {
        return $this->resourcesToField ?
                            $this->parseMessagePlaceholdersWhenToField() :
                            $this->getMailRenderer()->renderHtmlLayout();
    }

    /**
     * Parse the message for sending when via resource to field
     *
     * @return string
     */
    protected function parseMessagePlaceholdersWhenToField()
    {
        $message = $this->message;

        collect($this->getResourcesForPlaceholders())->map(function ($ids, $resourceName) {
            $resource = Innoclapps::resourceByName($resourceName);
            // At this time, only from the for associateable the placeholders are taken
            return $resource && count($ids) > 0 ? [
                'resource'     => $resource,
                'placeholders' => new MailPlaceholders($resource, $resource->displayQuery()->find($ids[0])),
            ] : null;
        })->filter()->unique(function ($resource) {
            return $resource['resource']->name();
        })->each(function ($resource) use (&$message) {
            $message = $resource['placeholders']->parseWhenViaInputFields($message);
        });

        return MailPlaceholders::cleanUpWhenViaInputFields($message);
    }

    /**
    * Action available fields
    *
    * @return array
    */
    public function fields() : array
    {
        $accounts = $this->repository()->pushCriteria(
            new EmailAccountsForUserCriteria(request()->user())
        )->all();

        $fields = [
            Select::make('email_account_id')->options(function () use ($accounts) {
                return $accounts->mapWithKeys(function ($account) {
                    return [$account->id => $account->email];
                });
            })
                ->withMeta(['attributes' => ['placeholder' => __('mail.workflows.fields.from_account')]])
                ->rules('required', 'in:' . $accounts->pluck('id')->implode(',')),

            Text::make('subject')
                ->withMeta(['attributes' => ['placeholder' => __('mail.workflows.fields.subject')]])
                ->rules('required'),

            MailEditor::make('message')
                ->withMeta(['attributes' => array_merge(
                    [
                        'placeholder' => __('mail.workflows.fields.message'),
                    ],
                    $this->resourcesToField ? [
                           'placeholders' => MailPlaceholders::createGroupsFromResources(
                               $this->filteredResourcesForPlaceholders()->all()
                           ),
                           'placeholders-disabled' => true,
                    ] : []
                ),
                ])
                ->rules('required'),
        ];

        if ($this->fieldsCallback) {
            foreach (array_reverse(Arr::wrap(call_user_func($this->fieldsCallback))) as $field) {
                array_unshift($fields, $field);
            }
        }

        array_unshift($fields, is_null($this->resourcesToField) ? $this->regularToField() : $this->resourcesToField);

        return $fields;
    }

    /**
     * Create regular To field
     *
     * @return \App\Innoclapps\Fields\Email
     */
    protected function regularToField()
    {
        return Email::make('to')
            ->withMeta(['attributes' => ['placeholder' => __('mail.workflows.fields.to')]])
            ->rules('required');
    }

    /**
     * Filtered resources from the TO field for placeholders
     *
     * @return \Illuminate\Support\Collection
     */
    protected function filteredResourcesForPlaceholders()
    {
        $resourceFromToField = array_keys($this->resourcesToField->getToResources());

        // Add the main model as resource from the TO field as it's resource as well
        // for the placeholders e.q. deal created => send email to contacts
        // the resources are the deals resource and the contacts resource
        if ($this->viaModelTrigger() && $modelResource = Innoclapps::resourceByModel($this->trigger()->model())) {
            $resourceFromToField[] = $modelResource->name();
        }

        // Filter non existent
        $nonExistent = array_diff($resourceFromToField, Innoclapps::getResourcesNames());

        // Resources that exists
        return collect(array_unique(array_diff($resourceFromToField, $nonExistent)));
    }

    /**
     * Get the email address(s) that email should be sent to
     *
     * @return mixed
     */
    protected function getEmailAddress()
    {
        if (is_null($this->resourcesToField)) {
            return $this->to; // regular email
        }

        return $this->recipientsWhenResourcesToField($this->model);
    }

    /**
     * Get the message associateable resources
     *
     * @return array|null
     */
    protected function getAssociateableResources()
    {
        if (! $this->resourcesToField || ! $this->viaModelTrigger()) {
            return;
        }

        // Primary model that the trigger was triggered for
        $associations = [
            $this->model::resource()->name() => [$this->model->getKey()],
        ];

        // Next we will check if there are associations available
        // to associate the actual message to other resources
        // e.q. deal created, send email to primary contact in this case
        // the associations to the message will be the deal and the first primary contact
        $resource = $this->resourcesToField->getToResources()[$this->to];

        // The actual resource model is User, we don't associate
        // as the user is not associateable or if the TO is the same as the resource e.q. contact send to contact email
        // as the contact is already associated above
        if ($resource::$model === User::class || $resource::$model === $this->model::class) {
            return $associations;
        }

        return array_merge($this->createPrimaryRecordFromResource($resource));
    }

    /**
     * Get resources available for placeholders
     *
     * @return array
     */
    protected function getResourcesForPlaceholders() : array
    {
        if (! $this->resourcesToField || ! $this->viaModelTrigger()) {
            return [];
        }

        $resources = [];

        // The resource the trigger was triggered for
        // The primary resources is also included in the filtered resources for placeholders
        $primaryResource = $this->model->resource();

        foreach ($this->filteredResourcesForPlaceholders() as $resourceName) {
            if ($primaryResource->name() === $resourceName) {
                $resources[$primaryResource->name()] = [$this->model->getKey()];
            } else {
                $resources = array_merge_recursive(
                    $resources,
                    $this->createPrimaryRecordFromResource(
                        $this->resourcesToField->getToResources()[$resourceName]
                    ),
                );
            }
        }

        return $resources;
    }

    /**
     * Create primary associations array from the given resource
     *
     * @param \App\Innoclapps\Resources\Resource $resource
     *
     * @return array
     */
    protected function createPrimaryRecordFromResource($resource)
    {
        // Only first resource record, the primary one
        return [
                $resource->name() => array_filter([
                    $this->model->{$resource->associateableName()}->first()?->getKey(),
                ]),
            ];
    }

    /**
     * Determine the To email addresses
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array|null
     */
    protected function recipientsWhenResourcesToField($model)
    {
        $resource = $this->resourcesToField->getToResources()[$this->to];

        // Same resource, e.q. company send email to company email
        if ($resource::$model === $this->model::class) { // or $this->to === 'self'
            if ($model instanceof User) {
                return $this->getEmailAddressWhenUserModel($model);
            } elseif (! $model::resource() instanceof HasEmail
                || ! $address = $model->{$model::resource()->emailAddressField()}) {
                return;
            }

            return $this->createAddress($address, $model);
        } elseif ($resource::$model === User::class) {
            // When using User, the To must be the relation name e.q. creator or user or any other relation name
            return $this->getEmailAddressWhenUserModel($this->model->{$this->to});
        }

        return $this->extractPrimaryResourceRecordEmailAddress($this->model->{$this->to}) ?: null;
    }

    /**
     * Get email addresses when user model
     *
     * @param \App\Models\User|null $user
     *
     * @return array|null
     */
    protected function getEmailAddressWhenUserModel($user) : ?array
    {
        if (! $user) {
            return null;
        }

        return $this->createAddress($user->email, $user->name);
    }

    /**
     * Get email address from resource associations/resources from the primary record
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     *
     * @return array
     */
    protected function extractPrimaryResourceRecordEmailAddress($models) : array
    {
        if ($models->isEmpty()) {
            return [];
        }

        // The first one is the primary record
        return with($models->first(), function ($primary) {
            if (! $primary ||
                    ! $primary->resource() instanceof HasEmail ||
                    ! $address = $primary->{$primary::resource()->emailAddressField()}) {
                return [];
            }

            return $this->createAddress($address, $primary);
        });
    }

    /**
     * Add To for for resources
     *
     * @param \App\Workflows\Actions\ResourcesSendEmailToField $field
     *
     * @return static
     */
    public function toResources(ResourcesSendEmailToField $field)
    {
        $this->resourcesToField = $field;

        return $this;
    }

    /**
     * Add fields to the send email action
     *
     * @param callable $callback
     *
     * @return static
     */
    public function withFields(callable $callback)
    {
        $this->fieldsCallback = $callback;

        return $this;
    }

    /**
     * Add available mail template placeholders
     *
     * @param callable $placeholders
     *
     * @return static
     */
    public function withPlaceholders(callable $callback)
    {
        $this->placeholders = $callback;

        return $this;
    }

    /**
     * Get the action placeholders when is not via resourceToField
     *
     * @return null|\App\Innoclapps\Placeholders\Collection
     */
    public function placeholders()
    {
        if (! $this->placeholders) {
            return;
        }

        if (is_callable($this->placeholders)) {
            $placeholders = call_user_func($this->placeholders, $this);
        } else {
            // When waking up from serialization
            $placeholders = $this->placeholders;
        }

        return $placeholders instanceof Collection ? $placeholders : new Collection($placeholders);
    }

    /**
     * Create address for the mailer
     *
     * @param string $address
     * @param \Illuminate\Database\Eloquent\Model|string $name
     *
     * @return array
     */
    protected function createAddress($address, $name)
    {
        return [
            'address' => $address,
            'name'    => $name instanceof Presentable ? $name->display_name : $name,
        ];
    }

    /**
     * Get the mail content rendered
     *
     * @return \App\Innoclapps\MailableTemplates\Renderer
     */
    protected function getMailRenderer() : Renderer
    {
        return app(Renderer::class, [
            'htmlTemplate' => $this->message,
            'subject'      => $this->subject, // currently not supported
            'placeholders' => $this->placeholders(),
        ]);
    }

    /**
     * Get the email account repository
     *
     * Using separate method because of serialization for jobs
     *
     * @return EmailAccountRepository
     */
    protected function repository()
    {
        return resolve(EmailAccountRepository::class);
    }

    /**
    * Action name
    *
    * @return string
    */
    public static function name() : string
    {
        return __('mail.workflows.actions.send');
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return array_merge(parent::jsonSerialize(), [
            'placeholders' => $this->placeholders(),
        ]);
    }

    /**
     * Prepare the instance for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        $properties = (new ReflectionClass($this))->getProperties();

        return array_values(array_filter(array_map(function ($p) {
            return ($p->isStatic() || in_array($name = $p->getName(), ['placeholders', 'fieldsCallback', 'resourcesToField'])) ? null : $name;
        }, $properties)));
    }

    /**
    * Wake up the instance from serialization
    *
    * @return array
    */
    public function __wakeup()
    {
        // Get the defined original action to replace data
        $action = $this->trigger()->getAction($this);

        $this->resourcesToField = $action->resourcesToField;
        $this->fieldsCallback   = $action->fieldsCallback;
        $this->placeholders     = $action->setData($this->data)->placeholders();
    }
}
