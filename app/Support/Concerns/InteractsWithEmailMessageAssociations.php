<?php

namespace App\Support\Concerns;

use App\Models\EmailAccountMessage;
use App\Innoclapps\Resources\Resource;
use App\Innoclapps\MailClient\Compose\AbstractComposer;
use App\Innoclapps\Contracts\MailClient\MessageInterface;
use App\Contracts\Repositories\EmailAccountMessageRepository;

trait InteractsWithEmailMessageAssociations
{
    /**
     * Associations separator
     *
     * @var string
     */
    protected string $associationsSeparator = ';';

    /**
     * When sending a message, we need to add the associations uuids
     * as headers to the message
     *
     * @param \App\Innoclapps\MailClient\Compose\AbstractComposer $composer
     * @param array $associations
     *
     * @return void
     */
    protected function addComposerAssociationsHeaders(AbstractComposer $composer, array $associations) : void
    {
        foreach (EmailAccountMessage::resource()->availableAssociations() as $resource) {
            if (isset($associations[$resource->name()])
                    && is_array($associations[$resource->name()])) {
                $associations = $associations[$resource->name()];
                if ($relatedModels = $this->getRelatedResourceModels($resource, $associations)) {
                    $composer->addHeader(
                        $this->createAssociationHeaderName($resource->name()),
                        implode($this->associationsSeparator, $relatedModels->pluck('uuid')->all())
                    );
                }
            }
        }
    }

    /**
     * Sync the message associations via the header
     *
     * @param int $databaseMesageId
     * @param \App\Innoclapps\Contracts\MailClient\MessageInterface $remoteMessage
     *
     * @return void
     */
    protected function syncAssociationsViaMessageHeaders(int $databaseMesageId, MessageInterface $remoteMessage) : void
    {
        $repository = resolve(EmailAccountMessageRepository::class);

        foreach (EmailAccountMessage::resource()->availableAssociations() as $resource) {
            if ($header = $remoteMessage->getHeader($this->createAssociationHeaderName($resource->name()))) {
                if (empty($header->getValue())) {
                    continue;
                }

                $repository->syncWithoutDetaching(
                    $databaseMesageId,
                    $resource->associateableName(),
                    $this->getAssociatedModelsViaHeaderUuids($resource, $header->getValue())
                );
            }
        }
    }

    /**
     * Get the associate models via the uuids
     *
     * @param \App\Innoclapps\Resources\Resource $resource
     * @param string $uuids The header value uuids
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAssociatedModelsViaHeaderUuids(Resource $resource, string $uuids)
    {
        $uuids = explode($this->associationsSeparator, $uuids);

        return $resource->repository()->findWhereIn('uuid', $uuids);
    }

    /**
     * Create associations header name for a given resource
     *
     * @param string $resourceName
     *
     * @return string
     */
    protected function createAssociationHeaderName($resourceName) : string
    {
        return 'X-Concord-' . ucfirst($resourceName) . '-Assoc';
    }

    /**
     * Get the related resource models
     *
     * @param \App\Innoclapps\Resources\Resource $resource
     * @param array $associations
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getRelatedResourceModels(Resource $resource, array $associations)
    {
        if (count($associations) === 0) {
            return false;
        }

        return $resource->repository()->findMany($associations);
    }
}
