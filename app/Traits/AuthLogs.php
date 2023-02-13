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
    $last_login_city = $this->LastLoginLocation()['city'] ?? null;

    $previous_login_country = $this->previousLoginLocation()['country'] ?? null;
    $last_login_country = $this->LastLoginLocation()['country'] ?? null;

    $agent_previous = new Agent();
    $agent_last = new Agent();

    $agent_previous->setUserAgent($previous_login_agent);
    $agent_last->setUserAgent($lastLoginAgent);

    $previous_login_device = $agent_previous->device();
    $last_login_device = $agent_last->device();


    if ($previous_login_device != $last_login_device) {
      return true;
    }

    // if ($previous_login_agent == null || $previous_login_ip == null || $previous_login_city == null || $previous_login_country == null) {
    //   return false;
    // }

    // if ($previous_login_agent !=  $lastLoginAgent) {
    //   return true;
    // }

    // if ($previous_login_ip !=  $last_login_ip) {

    //   return true;
    // }

    // if ($previous_login_city != $last_login_city) {
    //   return true;
    // }

    // if ($previous_login_country != $last_login_country) {
    //   return true;
    // }
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

  public function LastLoginLocation()
  {
    return optional($this->authentications()->first())->location;
  }

  public function previousLoginLocation()
  {
    return optional($this->authentications()->skip(1)->first())->location;
  }
}
