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

namespace App\Innoclapps;

use Illuminate\Http\Request;

class ReCaptcha
{
    /**
     * The ReCaptcha site key
     * @var string
     */
    protected ?string $siteKey = null;

    /**
     * The ReCaptcha secret key
     * @var string
     */
    protected ?string $secretKey = null;

    /**
     * IP addreses that validation should be skipped
     *
     * @var array
     */
    protected array $skippedIps = [];

    /**
     * Initialize new ReCaptcha instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Get the reCaptcha site key
     *
     * @return string|null
     */
    public function getSiteKey() : ?string
    {
        return $this->siteKey;
    }

    /**
     * Set the reCaptcha site key
     *
     * @param string $key
     *
     * @return static
     */
    public function setSiteKey(?string $key) : static
    {
        $this->siteKey = $key;

        return $this;
    }

    /**
     * Get the recaptcha secret key
     *
     * @return string|null
     */
    public function getSecretKey() : ?string
    {
        return $this->secretKey;
    }

    /**
    * Set the reCaptcha secret key
    *
    * @param string $key

    * @return static
    */
    public function setSecretKey(?string $key) : static
    {
        $this->secretKey = $key;

        return $this;
    }

    /**
     * Get an array of IP addresses that a reCaptcha should be skipped
     *
     * @return array
     */
    public function getSkippedIps() : array
    {
        return array_filter(array_map('trim', $this->skippedIps));
    }

    /**
     * Set IP addresses that a reCaptcha should be skipped
     *
     * @param array|string $ips
     *
     * @return static
     */
    public function setSkippedIps(array|string $ips)
    {
        if (is_string($ips)) {
            $ips = explode(',', $ips);
        }

        $this->skippedIps = $ips;

        return $this;
    }

    /**
     * Determine whether the reCaptcha validation should be skipped
     *
     * @param string|null $ip
     *
     * @return boolean
     */
    public function shouldSkip(?string $ip = null) : bool
    {
        return in_array($ip ?? $this->request->getClientIp(), $this->getSkippedIps());
    }

    /**
     * Check whether the reCaptcha is configured
     *
     * @return boolean
     */
    public function configured() : bool
    {
        return ! empty($this->getSiteKey()) && ! empty($this->getSecretKey());
    }

    /**
     * Determine whether the reCaptcha validation should be shown
     *
     * @param string|null $ip
     *
     * @return boolean
     */
    public function shouldShow(?string $ip = null) : bool
    {
        if (! $this->configured()) {
            return false;
        }

        return ! $this->shouldSkip($ip);
    }
}
