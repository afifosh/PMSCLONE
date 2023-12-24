<?php

namespace App\Models;

use App\Mail\FormSubmitEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Form extends Model
{
    use HasFactory;

    public $fillable = [
        'title', 'json', 'logo', 'success_msg', 'thanks_msg', 'email', 'amount', 'currency_symbol', 'currency_name', 'theme', 'theme_color', 'theme_background_image',
        'payment_status', 'payment_type', 'bccemail', 'ccemail', 'allow_comments', 'allow_share_section', 'assign_type', 'created_by', 'set_end_date', 'set_end_date_time',
    ];

    public function getFormArray()
    {
        return json_decode($this->json);
    }

    public function Roles()
    {
        return $this->belongsToMany('Spatie\Permission\Models\Role', 'user_forms', 'form_id', 'role_id');
    }

    public function User()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function assignFormRoles($roleIds)
    {
        $roles = $this->Roles->pluck('name', 'id')->toArray();
        if ($roleIds) {
            foreach ($roleIds as $id) {
                if (!array_key_exists($id, $roles)) {
                    UserForm::create(['form_id' => $this->id, 'role_id' => $id]);
                } else {
                    unset($roles[$id]);
                }
            }
        }
        if ($roles) {
            foreach ($roles as $id => $name) {
                UserForm::where(['form_id' => $this->id, 'role_id' => $id])->delete();
            }
        }
    }

    public function commmant()
    {
        return $this->hasMany(FormComments::class, 'form_id', 'id');
    }

    //assign form user
    public function assignedusers()
    {
        return $this->belongsToMany(User::class, 'assign_forms_users', 'form_id', 'user_id');
    }

    public function assignUser($usersIds)
    {
        $formUsers = $this->assignedusers->pluck('name', 'id')->toArray();
        if ($usersIds) {
            foreach ($usersIds as $id) {
                if (!array_key_exists($id, $formUsers)) {
                    AssignFormsUsers::create(['form_id' => $this->id, 'user_id' => $id]);
                } else {
                    unset($form_users[$id]);
                }
            }
        }
        if ($formUsers) {
            foreach ($formUsers as $id => $name) {
                AssignFormsUsers::where(['form_id' => $this->id, 'user_id' => $id])->delete();
            }
        }
    }

    //assign form roles
    public function assignedroles()
    {
        return $this->belongsToMany('Spatie\Permission\Models\Role', 'assign_forms_roles', 'form_id', 'role_id');
    }

    public function assignRole($usersIds)
    {
        $formRoles = $this->assignedroles->pluck('name', 'id')->toArray();
        if ($usersIds) {
            foreach ($usersIds as $id) {
                if (!array_key_exists($id, $formRoles)) {
                    AssignFormsRoles::create(['form_id' => $this->id, 'role_id' => $id]);
                } else {
                    unset($form_roles[$id]);
                }
            }
        }
        if ($formRoles) {
            foreach ($formRoles as $id => $name) {
                AssignFormsRoles::where(['form_id' => $this->id, 'role_id' => $id])->delete();
            }
        }
    }

    public static function integrationFormData($form, $formValue)
    {
        //sendgrid integration
        $formsendgridsetting = FormIntegrationSetting::where('key', 'sendgrid_integration')->where('form_id', $form->id)->where('status', 1)->first();
        $formVale = [];
        if ($formsendgridsetting) {
            if ($formsendgridsetting->json) {
                $sendgridFieldJsons = json_decode($formsendgridsetting->field_json, true);
                $sendgridJsons = json_decode($formsendgridsetting->json, true);
                foreach ($sendgridJsons as $sendgridJsonkey => $sendgridJson) {
                    if ($sendgridJson['sendgrid_email'] && $sendgridJson['sendgrid_host'] && $sendgridJson['sendgrid_port'] && $sendgridJson['sendgrid_username'] && $sendgridJson['sendgrid_password'] && $sendgridJson['sendgrid_encryption'] && $sendgridJson['sendgrid_from_address'] && $sendgridJson['sendgrid_from_name']) {
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJsonkgrid => $formValueJson) {
                            foreach ($formValueJson as $formValueJsonk1grid => $formValue) {
                                foreach ($sendgridFieldJsons as $sendgridFieldkey => $sendgridFieldJson) {
                                    if ($sendgridFieldkey == $sendgridJsonkey) {
                                        $sendgridarr = explode(',', $sendgridFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $sendgridarr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $formVale[$formValueJsonkgrid][$formValueJsonk1grid] = $formValue;
                                                    }
                                                }
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'file'  && $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'SignaturePad' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $sendgridarr)) {
                                                $formVale[$formValueJsonkgrid][$formValueJsonk1grid] = $formValue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        config([
                            'mail.default'                 => 'sendgrid',
                            'mail.mailers.smtp.host'       => $sendgridJson['sendgrid_host'],
                            'mail.mailers.smtp.port'       => $sendgridJson['sendgrid_port'],
                            'mail.mailers.smtp.encryption' => $sendgridJson['sendgrid_encryption'],
                            'mail.mailers.smtp.username'   => $sendgridJson['sendgrid_username'],
                            'services.sendgrid.api_key'    => $sendgridJson['sendgrid_password'],
                            'mail.from.address'            => $sendgridJson['sendgrid_from_address'],
                            'mail.from.name'               => $sendgridJson['sendgrid_from_name'],
                        ]);
                        try {
                            Mail::to($sendgridJson['sendgrid_email'])->send(new FormSubmitEmail($formValue, $formVale));
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }
    }

    public function scopeApplyRequestFilters($q)
    {
      //
    }
}
