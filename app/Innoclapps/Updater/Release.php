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

use Illuminate\Filesystem\Filesystem;

final class Release
{
    use DownloadsFiles;

    /**
     * @var \App\Innoclapps\Updater\ZipArchive
     */
    protected $archive;

    /**
     * Initialize new Relase instance
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(protected string $version, protected Filesystem $filesystem)
    {
    }

    /**
     * @return string
     */
    public function getVersion() : string
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return static
     */
    public function setVersion(string $version) : static
    {
        $this->version = $version;

        return $this;
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
}
