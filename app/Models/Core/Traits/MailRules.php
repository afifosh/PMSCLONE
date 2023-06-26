<?php


namespace App\Models\Core\Traits;


trait MailRules
{

    protected $basicRules = [
            'from_email' => 'required|email',
            'from_name' => 'required|min:3',
    ];

    public function mailgunRules()
    {
        return array_merge([
            'domain_name' => 'required|min:3',
            'api_key' => 'required|min:3'
        ], $this->basicRules);
    }

    public function amazonSesRules()
    {
        return array_merge([
            'hostname' => 'required|min:3',
            'access_key_id' => 'required|min:3',
            'secret_access_key' => 'required|min:3',
            'region' => 'required|min:3',
        ], $this->basicRules);
    }

    public function smtpRules()
    {
        return array_merge([
            'username' => 'required|min:3',
            'host' => 'required|min:3',
            'port' => 'required|min:3',
            'password' => 'required|min:3',
            'encryption' => 'required|min:3',
        ], $this->basicRules);
    }

    public function sendmailRules()
    {
        return $this->basicRules;
    }
}
