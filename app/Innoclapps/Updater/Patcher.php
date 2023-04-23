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

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use App\Innoclapps\Updater\Exceptions\UpdaterException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Innoclapps\Updater\Exceptions\PurchaseKeyEmptyException;
use App\Innoclapps\Updater\Exceptions\InvalidPurchaseKeyException;
use App\Innoclapps\Updater\Exceptions\HasWrongPermissionsException;
use App\Innoclapps\Updater\Exceptions\CannotOpenZipArchiveException;

class Patcher
{
    use ChecksPermissions, ExchangesPurchaseKey;

    /**
     * @var int
     */
    const INVALID_PURCHASE_KEY_CODE = 498;

    /**
     * @var int
     */
    const PURCHASE_KEY_EMPTY_CODE = 497;

    /**
     * Available patches
     *
     * @var array
     */
    protected array $patches = [];

    /**
     * The path where the update files will be extracted
     *
     * @var string
     */
    protected string $basePath;

    /**
     * Initialize new Patcher instance.
     *
     * @param \GuzzleHttp\Client $client
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param array $config
     */
    public function __construct(protected Client $client, protected Filesystem $filesystem, protected array $config)
    {
        $this->basePath = base_path();
        $filesystem->ensureDirectoryExists($config['download_path']);
    }

    /**
     * Download the given patch by token
     *
     * @param string $token
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $token) : BinaryFileResponse
    {
        $patch = $this->find($token);

        $this->makeDownloadRequest($patch);

        return response()->download(
            $patch->getStoragePath(),
            'v' . $this->config['version_installed'] . '-' . basename($patch->getStoragePath())
        )->deleteFileAfterSend(true);
    }

    /**
     * Retrieve the given patch by token
     *
     * @param string|\App\Innoclapps\Updater\Patch $token
     *
     * @return \App\Innoclapps\Updater\Patch
     *
     * @throws \App\Innoclapps\Updater\Exceptions\UpdaterException
     */
    public function fetch(Patch|string $patch) : Patch
    {
        if (is_string($patch)) {
            $patch = $this->find($patch);
        }

        if (! $patch->archive()->exists()) {
            $this->makeDownloadRequest($patch);
        }

        return $patch;
    }

    /**
     * Find patch by the given token
     *
     * @param string $token
     *
     * @return \App\Innoclapps\Updater\Patch
     */
    public function find(string $token) : Patch
    {
        $patches = $this->getAvailablePatches();

        return tap($patches->first(function ($patch) use ($token) {
            return $patch->token() === $token;
        }), function ($patch) use ($token) {
            throw_if(is_null($patch), new UpdaterException("The patch {$token} could not be found.", 404));
        });
    }

    /**
     * Apply the given patch
     *
     * @param \App\Innoclapps\Updater\Patch|string $patch
     *
     * @return boolean
     *
     * @throws \App\Innoclapps\Updater\Exceptions\HasWrongPermissionsException
     * @throws \App\Innoclapps\Updater\Exceptions\UpdaterException
     */
    public function apply(Patch|string $patch) : bool
    {
        if (! $this->checkPermissions($this->basePath, $this->config['permissions']['exclude_folders'])) {
            throw new HasWrongPermissionsException;
        }

        if (is_string($patch)) {
            $patch = $this->fetch($patch);
        }

        throw_if(
            $patch->version() != $this->config['version_installed'],
            new UpdaterException('This patch does not belongs to the current version.', 409)
        );

        if (! $patch->archive()->exists()) {
            $this->makeDownloadRequest($patch);
        }

        try {
            $patch->archive()->extract($this->basePath);
        } catch (CannotOpenZipArchiveException $e) {
            // Delete the source in case of invalid .zip archive
            $patch->archive()->deleteSource();

            throw $e;
        }

        $patch->markAsApplied();

        return true;
    }

    /**
     * Get the available patches for the version
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \App\Innoclapps\Updater\Exceptions\UpdaterException
     */
    public function getAvailablePatches() : Collection
    {
        $version = $this->config['version_installed'];

        if (array_key_exists($version, $this->patches)) {
            return $this->patches[$version];
        }

        if (empty($this->config['patches_url'])) {
            throw new UpdaterException('Patches URL not specified, please enter a valid URL in your config.', 500);
        }

        try {
            $response = $this->client->get($this->config['patches_url'] . '/' . $version, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
        } catch (RequestException $e) {
            throw new UpdaterException($e->getMessage(), $e->getCode());
        }

        $patches = json_decode($response->getBody()->getContents());

        return $this->patches[$version] = collect($patches)->map(function ($patch) {
            $patch->date = Carbon::parse($patch->date);

            return $patch;
        })->map(function ($patch) use ($version) {
            $downloadUrl = Updater::createInternalRequestUrl(
                $this->config['patches_url'] . '/' . $version . '/' . $patch->token
            );

            return (new Patch($patch))
                ->setDownloadUrl($downloadUrl)
                ->setAccessToken($this->getPurchaseKey())
                ->setStoragePath(
                    Str::finish($this->config['download_path'], DIRECTORY_SEPARATOR) . $patch->token . '.zip'
                );
        })->sortBy([
            [fn ($patch) => $patch->isApplied(), 'asc'],
            ['date', 'asc'],
        ])->values();
    }

    /**
     * Download the patch
     *
     * @param \App\Innoclapps\Updater\Patch $patch
     *
     * @return void
     */
    protected function makeDownloadRequest(Patch $patch) : void
    {
        try {
            $patch->download($this->client);
        } catch (ClientException $e) {
            if ($e->getCode() === static::INVALID_PURCHASE_KEY_CODE) {
                throw new InvalidPurchaseKeyException;
            } elseif ($e->getCode() === static::PURCHASE_KEY_EMPTY_CODE) {
                throw new PurchaseKeyEmptyException;
            }

            throw $e;
        }
    }
}
