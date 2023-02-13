<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmailServiceRequest extends FormRequest
{
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
    public function rules()
    {
        return [
            'service' => 'required|in:ses,mailgun,smtp,sendmail,mailtrap|exists:email_services,service',
            'email_sent_from_name' => 'required',
            'email_sent_from_email' => 'required|email',
            'ses_host' => 'required_if:service,ses',
            'access_key_id' => 'required_if:service,ses',
            'secret_access_key' => 'required_if:service,ses',
            'region' => 'required_if:service,ses',
            'domain_name' => 'required_if:service,mailgun',
            'api_key' => 'required_if:service,mailgun',
            'username' => 'required_if:service,smtp',
            'host' => 'required_if:service,smtp',
            'port' => 'required_if:service,smtp',
            'password' => 'required_if:service,smtp',
            'encryption_key' => 'required_if:service,smtp',
        ];
    }
}
