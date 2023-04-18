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

namespace App\Innoclapps\Updater;

use JsonSerializable;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use App\Innoclapps\Contracts\Repositories\PatchRepository;

final class Patch implements Arrayable, JsonSerializable
{
    use DownloadsFiles;

    /**
     * @var \App\Innoclapps\Updater\ZipArchive
     */
    protected $archive;

    /**
     * @var App\Innoclapps\Contracts\Repositories\PatchRepository
     */
    protected $repository;

    /**
     * Initialize new Relase instance
     *
     * @param \stdClass $patch
     */
    public function __construct(protected object $patch)
    {
        $this->repository = app(PatchRepository::class);
    }

    /**
     * Check whether the patch is applied
     *
     * @return boolean
     */
    public function isApplied() : bool
    {
        return $this->repository->isApplied($this->token());
    }

    /**
     * Mark patch as applied
     *
     * @return boolean
     */
    public function markAsApplied()
    {
        $this->repository->create([
            'token'   => $this->token(),
            'version' => $this->version(),
        ]);

        return true;
    }

    /**
     * Get the patch token
     *
     * @return string
     */
    public function token() : string
    {
        return $this->patch->token;
    }

    /**
     * Get the patch description
     *
     * @return string
     */
    public function description() : string
    {
        return $this->patch->description;
    }

    /**
     * Get the patch date
     *
     * @return \Illuminate\Support\Carbon
     */
    public function date() : Carbon
    {
        return $this->patch->date;
    }

    /**
     * Get the patch version
     *
     * @return string
     */
    public function version() : string
    {
        return $this->patch->version;
    }

    /**
     * Get the release archive
     *
     * @return \App\Innoclapps\Updater\ZipArchive
     */
    public function archive() : ZipArchive
    {
        if ($this->archive) {
            return $this->archive;
        }

        return $this->archive = new ZipArchive($this->getStoragePath());
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'description' => $this->description(),
            'date'        => $this->date()->toJSON(),
            'token'       => $this->token(),
            'isApplied'   => $this->isApplied(),
        ];
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }
}
