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

namespace App\Http\Requests;

use App\Models\EmailAccount;
use Illuminate\Validation\Rule;
use App\Innoclapps\Rules\UniqueRule;
use Illuminate\Validation\Rules\Enum;
use App\Installer\RequirementsChecker;
use Illuminate\Foundation\Http\FormRequest;
use App\Innoclapps\MailClient\ClientManager;
use App\Innoclapps\MailClient\ConnectionType;
use App\Contracts\Repositories\EmailAccountRepository;

class EmailAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'connection_type'   => [Rule::requiredIf($this->isMethod('POST')), new Enum(ConnectionType::class)],
            'email'             => $this->getEmailFieldRules(),
            'password'          => $this->route('account') ? 'nullable' : 'required',
            'from_name_header'  => $this->getFromNameHeaderRules(),
            'initial_sync_from' => $this->getInitialSyncFromRules(),
            'imap_server'       => ['max:191', $this->getRequiredIfRuleForImapField()],
            'imap_port'         => [$this->getRequiredIfRuleForImapField(), 'numeric'],
            'imap_encryption'   => ['nullable', Rule::in(ClientManager::ENCRYPTION_TYPES)],
            'smtp_server'       => ['max:191', $this->getRequiredIfRuleForImapField()],
            'smtp_port'         => [$this->getRequiredIfRuleForImapField(), 'numeric'],
            'smtp_encryption'   => ['nullable', Rule::in(ClientManager::ENCRYPTION_TYPES)],
            'validate_cert'     => 'boolean|nullable',
            'folders'           => ['array', $this->getRequiredIfRuleForImapField()],
        ];
    }

    /**
    * Configure the validator instance.
    *
    * @param \Illuminate\Validation\Validator  $validator
    * @return void
    */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isImapConnectionType() && ! (new RequirementsChecker)->passes('imap')) {
                abort(409, 'In order to use IMAP account type, you will need to enable the PHP extension "imap".');
            }
        });
    }

    /**
     * Get the email field validation rules
     *
     * @return array
     */
    protected function getEmailFieldRules()
    {
        return  [
           'email',
           'max:191',
            Rule::requiredIf(function () {
                // Not required as the email can't be updated once
                // the account is created
                if ($this->isMethod('PUT')) {
                    return false;
                }

                return $this->isImapConnectionType();
            }),
            UniqueRule::make(EmailAccount::class, 'account'),
        ];
    }

    /**
     * Get the form_name_header field rule
     * NOTE: from_name_header field is only for shared email accounts
     *
     * @return Rule
     */
    protected function getFromNameHeaderRules()
    {
        return Rule::requiredIf(function () {
            if ($this->isMethod('POST') && $this->isSharedAccountRequest()) {
                return true;
            } elseif ($this->isMethod('PUT')) {
                $account = resolve(EmailAccountRepository::class)->find($this->route('account'));

                return $account->isShared();
            }

            return false;
        });
    }

    /**
     * Get the intial_sync_period field rule
     *
     * @return Rule
     */
    protected function getInitialSyncFromRules()
    {
        return [
            'date',
            function ($attribute, $value, $fail) {
                if ($this->isMethod('PUT')) {
                    return;
                }
                // API Usage
                if ($value && strtotime($value) < strtotime('-6 months')) {
                    $fail('The initial synchronization date must not be older then 6 months.');
                }
            },
            Rule::requiredIf($this->isMethod('POST')),
        ];
    }

    /**
     * Get the requiredIf rule for the IMAP connection type fields
     *
     * @return \Illuminate\Validation\Rules\RequiredIf
     */
    protected function getRequiredIfRuleForImapField()
    {
        return Rule::requiredIf($this->isImapConnectionType());
    }

    /**
     * Check whether the account uses IMAP connection
     *
     * @return boolean
     */
    protected function isImapConnectionType()
    {
        return $this->connection_type === ConnectionType::Imap->value;
    }

    /**
     * Check whether the request is for creation shared account
     *
     * @return boolean
     */
    protected function isSharedAccountRequest()
    {
        return is_null($this->user_id);
    }
}
