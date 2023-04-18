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

use ZipArchive as BaseZipArchive;
use Illuminate\Filesystem\Filesystem;
use App\Innoclapps\Updater\Exceptions\UpdaterException;
use App\Innoclapps\Updater\Exceptions\FailedToExtractZipException;
use App\Innoclapps\Updater\Exceptions\CannotOpenZipArchiveException;

class ZipArchive
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var array
     */
    protected array $excludedDirectories = [];

    /**
     * @var array
     */
    protected array $excludedFiles = [];

    /**
     * Create new ZipArchive instance
     *
     * @param string $path
     */
    public function __construct(protected string $path)
    {
        $this->filesystem = new Filesystem;
    }

    /**
     * Set the excluded directories
     *
     * @param string|array $dirs
     *
     * @return static
     */
    public function excludedDirectories(string|array $dirs) : static
    {
        $this->excludedDirectories = is_array($dirs) ? $dirs : func_get_args();

        return $this;
    }

    /**
     * Set the excluded files
     *
     * @param string|array $files
     *
     * @return static
     */
    public function excludedFiles(string|array $files) : static
    {
        $this->excludedFiles = is_array($files) ? $files : func_get_args();

        return $this;
    }

    /**
     * Extract the zip archive to the given path
     *
     * @param string $to
     * @param boolean $deleteSource
     *
     * @return boolean
     */
    public function extract(string $to, bool $deleteSource = true) : bool
    {
        $extension = pathinfo($this->path, PATHINFO_EXTENSION);

        if (preg_match('/[zZ]ip/', $extension)) {
            $extracted = $this->perform($to);

            if ($extracted && $deleteSource) {
                $this->deleteSource();
            }

            if (! $extracted) {
                throw new FailedToExtractZipException($this->path);
            }

            return true;
        }

        throw new UpdaterException('File is not a zip archive. File is ' . $extension . '.');
    }

    /**
     * Perform the zip extraction
     *
     * @param string $to
     *
     * @return boolean
     */
    protected function perform(string $to) : bool
    {
        $this->removeExcludedFilesAndDirectories();

        $zip = new BaseZipArchive;

        if (true !== ($zip->open($this->path))) {
            throw new CannotOpenZipArchiveException($this->path);
        }

        $extracted = $zip->extractTo($to);

        $zip->close();

        return $extracted;
    }

    /**
     * Delete the .zip source file
     *
     * @return static
     */
    public function deleteSource()
    {
        $this->filesystem->delete($this->path);

        return $this;
    }

    /**
     * Remove the excluded files and directories
     *
     * @return void
     */
    protected function removeExcludedFilesAndDirectories()
    {
        foreach ($this->excludedDirectories as $excludedDir) {
            $this->deleteDirectory(str_replace('/', DIRECTORY_SEPARATOR, $excludedDir));
        }

        foreach ($this->excludedFiles as $excludedFile) {
            $this->deleteFile(str_replace('/', DIRECTORY_SEPARATOR, $excludedFile));
        }
    }

    /**
     * Delete file from the zip archive
     *
     * @param string $name
     *
     * @return void
     */
    protected function deleteFile(string $name)
    {
        $this->deleteFromZip($name, false);
    }

    /**
     * Delete directory from the zip archive
     *
     * @param string $name
     *
     * @return void
     */
    protected function deleteDirectory(string $name)
    {
        $this->deleteFromZip($name, true);
    }

    /**
     * Removes an entry from the zip file.
     *
     * The name is the relative path of the entry to remove (relative to the zip's root).
     *
     * @param string $name
     * @param bool $isDir
     *
     * @return void
     */
    protected function deleteFromZip(string $name, bool $isDir)
    {
        $zip = new BaseZipArchive;

        if (true !== ($zip->open($this->path))) {
            throw new CannotOpenZipArchiveException($this->path);
        }

        $name = rtrim($name, DIRECTORY_SEPARATOR);

        if (true === $isDir) {
            $name .= DIRECTORY_SEPARATOR;
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $info = $zip->statIndex($i);

            if (true === str_starts_with($info['name'], $name)) {
                $zip->deleteIndex($i);
            }
        }

        $zip->close();
    }

    /**
     * Check if the archive file exist
     *
     * @return boolean
     */
    public function exists() : bool
    {
        // Check if source archive is there but not extracted
        // We will check also the size of the zip, it can be 0 when
        // an exception is thrown via the request and the sink file will remain undeleted
        // with size 0, in this case, we need cast this version source as non-existed to allow future requests
        // for re-retrieval the release from the remote archive
        if ($this->filesystem->exists($this->path)) {
            return $this->filesystem->size($this->path) > 0;
        }

        return false;
    }
}
