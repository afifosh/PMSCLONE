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
            'name' => 'required|in:ses,mailgun,smtp,sendmail,mailtrap|exists:email_services,name',
            'sent_from_name' => 'required',
            'sent_from_address' => 'required|email',
            // ses
            'ses_host' => 'required_if:service,ses',
            'ses_access_key_id' => 'required_if:service,ses',
            'ses_secret_access_key' => 'required_if:service,ses',
            'ses_region' => 'required_if:service,ses',
            // mailgun
            'mailgun_domain_name' => 'required_if:service,mailgun',
            'mailgun_api_key' => 'required_if:service,mailgun',
            // smtp
            'smtp_username' => 'required_if:service,smtp',
            'smtp_host' => 'required_if:service,smtp',
            'smtp_port' => 'required_if:service,smtp',
            'smtp_password' => 'required_if:service,smtp',
            'smtp_encryption' => 'required_if:service,smtp',
            // mailtrap
            'mailtrap_username' => 'required_if:service,mailtrap',
            'mailtrap_host' => 'required_if:service,mailtrap',
            'mailtrap_port' => 'required_if:service,mailtrap',
            'mailtrap_password' => 'required_if:service,mailtrap',
            'mailtrap_encryption' => 'required_if:service,mailtrap',
        ];
    }
}
