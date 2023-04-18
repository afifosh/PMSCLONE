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

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use App\Innoclapps\Updater\Exceptions\UpdaterException;

trait DownloadsFiles
{
    /**
     * @var string|null
     */
    protected ?string $accessTokenPrefix = 'Bearer ';

    /**
     * @var string|null
     */
    protected ?string $accessToken = null;

    /**
     * Url to download the release from.
     *
     * @var string|null
     */
    protected ?string $downloadUrl = null;

    /**
     * Path to download the release to.
     * Example: /tmp/release-1.1.zip.
     *
     * @var string|null
     */
    protected ?string $storagePath = null;

    /**
     * @return string|null
     */
    public function getDownloadUrl() : ?string
    {
        return $this->downloadUrl;
    }

    /**
     * @param string $downloadUrl
     *
     * @return static
     */
    public function setDownloadUrl(string $downloadUrl) : static
    {
        $this->downloadUrl = $downloadUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getStoragePath() : ?string
    {
        return $this->storagePath;
    }

    /**
     * @param string $storagePath
     * @param boolean $createDirectory
     *
     * @return static
     */
    public function setStoragePath(string $storagePath) : static
    {
        $this->storagePath = $storagePath;

        return $this;
    }

    /**
     * Download the update file
     *
     * @param \GuzzleHttp\ClientInterface $client
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function download(ClientInterface $client) : ResponseInterface
    {
        throw_if(empty($this->getStoragePath()), new UpdaterException('No storage path set.', 500));
        throw_if(empty($this->getDownloadUrl()), new UpdaterException('Download URL not provided.', 500));

        return $client->request(
            'GET',
            $this->getDownloadUrl(),
            [
                'sink'    => $this->getStoragePath(),
                'headers' => $this->hasAccessToken() ? ['Authorization' => $this->getAccessToken()] : [],
            ]
        );
    }

    /**
     * Get the access token
     *
     * @param boolean $withPrefix
     *
     * @return string|null
     */
    public function getAccessToken(bool $withPrefix = true) : ?string
    {
        if ($withPrefix && $this->accessTokenPrefix) {
            return $this->accessTokenPrefix . $this->accessToken;
        }

        return $this->accessToken;
    }

    /**
     * Set the access token
     *
     * @param string $token
     *
     * @return static
     */
    public function setAccessToken(string $token) : static
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Check whether there is access token configured
     *
     * @return boolean
     */
    public function hasAccessToken() : bool
    {
        return ! empty($this->accessToken);
    }

    /**
     * Set custom access token prefix
     *
     * @param string|null $prefix
     *
     * @return static
     */
    public function setAccessTokenPrefix(?string $prefix) : static
    {
        $this->accessTokenPrefix = $prefix;

        return $this;
    }

    /**
     * Get the access token prefix
     *
     * @return string|null
     */
    public function getAccessTokenPrefix() : ?string
    {
        return $this->accessTokenPrefix;
    }
}
