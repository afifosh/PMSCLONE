<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules['broadcast_driver'] = 'required';

        if ($this->broadcast_driver === 'pusher') {
            $rules['pusher_app_id'] = 'required';
            $rules['pusher_app_key'] = 'required';
            $rules['pusher_app_secret'] = 'required';
            $rules['pusher_app_cluster'] = 'required';
        }
        if ($this->broadcast_driver === 'websockets') {
          $rules['pusher_app_id'] = 'required';
          $rules['pusher_app_key'] = 'required';
          $rules['pusher_app_secret'] = 'required';
          $rules['pusher_app_cluster'] = 'required';
          $rules['app_scheme'] = 'required';
          $rules['app_host'] = 'required';
          $rules['app_port'] = 'required';
      }

        return $rules;
    }
}
