<?php

namespace App\Traits;

use Jenssegers\Agent\Agent;

trait AuthLogs
{

  public function checkIfLastLoginDetailsChanged()
  {
    $previous_login_agent = $this->previousUserAgent();
    $lastLoginAgent = $this->lastLoginAgent();

    $previous_login_ip = $this->previousLoginIp();
    $last_login_ip = $this->lastLoginIp();


    $previous_login_city = $this->previousLoginLocation()['city'] ?? null;
    $last_login_city = $this->lastLoginLocation()['city'] ?? null;

    $previous_login_country = $this->previousLoginLocation()['country'] ?? null;
    $last_login_country = $this->lastLoginLocation()['country'] ?? null;

    $agent_previous = new Agent();
    $agent_last = new Agent();

    $agent_previous->setUserAgent($previous_login_agent);
    $agent_last->setUserAgent($lastLoginAgent);

    $previous_login_device = $agent_previous->device();
    $last_login_device = $agent_last->device();


    if ($previous_login_city == null || $previous_login_city == '') {
      return false;
    }
    if ($previous_login_country == null || $previous_login_country == '') {
      return false;
    }

    if ($previous_login_device == null || $previous_login_device == "") {
      return false;
    }

    // \Log::debug("previous_login_city: " . $previous_login_city);
    // \Log::debug("previous_login_country: " . $previous_login_country);
    // \Log::debug("previous_login_device: " . $previous_login_device);

    // \Log::debug("last_login_city: " . $last_login_city);
    // \Log::debug("last_login_country: " . $last_login_country);
    // \Log::debug("last_login_device: " . $last_login_device);


    if ($previous_login_city != $last_login_city) {
      return true;
    }

    if ($previous_login_country != $last_login_country) {
      return true;
    }

    if ($previous_login_device != $last_login_device) {
      return true;
    }

    return false;
  }


  public function lastLoginAgent()
  {
    return optional($this->authentications()->first())->user_agent;
  }

  public function previousUserAgent()
  {
    return optional($this->authentications()->skip(1)->first())->user_agent;
  }

  public function lastLoginLocation()
  {
    return optional($this->authentications()->first())->location;
  }

  public function previousLoginLocation()
  {
    return optional($this->authentications()->skip(1)->first())->location;
  }
}
