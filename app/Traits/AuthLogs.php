<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait AuthLogs
{
	/**
	 * Checks if user is trying to login from new location
	 * 
	 * @return bool
	 */
	public function findIfLoginIpUpdated(): bool
	{
		$previousSuccessfulLoginIp = $this->previousSuccessfulLoginIp();

		if (!$previousSuccessfulLoginIp) {
			return false;
		}

		return $this->lastSuccessfulLoginIp() !== $previousSuccessfulLoginIp;
	}

	/**
	 * Get previous successful location 
	 * 
	 * @return string
	 */
	private function previousSuccessfulLoginIp(): string|null
	{
		return optional(
			$this->authentications()->whereLoginSuccessful(true)->skip(1)->first()
		)->ip_address;
	}
}
