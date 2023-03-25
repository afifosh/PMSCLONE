<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class DeliverySettingRequest extends FormRequest
{
    /**
     * basic validation rules
     * 
     * @var array
     */
    protected $basicRules = [
        'from_email' => 'required|email',
        'from_name' => 'required|min:3',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->provider == 'mailgun')
            return $this->mailgunRules();
        elseif ($this->provider == 'amazon_ses')
            return $this->amazonSesRules();
        elseif ($this->provider == 'smtp' || $this->provider == 'mailtrap')
            return $this->smtpRules();
        else
            return ['provider' => 'required'];
    }

    /**
     * mailgun validation rules
     * 
     * @return array
     */
    private function mailgunRules(): array
    {
        return array_merge([
            'domain_name' => 'required|min:3',
            'api_key' => 'required|min:3'
        ], $this->basicRules);
    }

    /**
     * amazon ses validation rules
     * 
     * @return array
     */
    private function amazonSesRules(): array
    {
        return array_merge([
            'host' => 'required|min:3',
            'access_key_id' => 'required|min:3',
            'secret_access_key' => 'required|min:3',
        ], $this->basicRules);
    }

    /**
     * smtp validation rules
     * 
     * @return array
     */
    private function smtpRules(): array
    {
        return array_merge([
            'username' => 'required|min:3',
            'password' => 'required|min:3',
            'host' => 'required|min:3',
            'port' => 'required|min:3',
            'encryption' => 'required|min:3',
        ], $this->basicRules);
    }
}
