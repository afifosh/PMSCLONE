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

namespace App\Http\Controllers\EmailAccount;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Installer\RequirementsChecker;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Innoclapps\MailClient\ClientManager;
use App\Innoclapps\MailClient\ConnectionType;
use App\Innoclapps\MailClient\Imap\SmtpConfig;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Innoclapps\MailClient\Imap\Config as ImapConfig;
use App\Innoclapps\MailClient\Exceptions\ConnectionErrorException;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class EmailAccountConnectionTestController extends Controller
{
    /**
     * @var array
     */
    protected $imapFolders = [];

    /**
     * Test the account connection
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        if (! (new RequirementsChecker)->passes('imap')) {
            abort(409, 'In order to use IMAP account type, you will need to enable the PHP extension "imap".');
        }

        $validator = Validator::make($request->all(), [
            'connection_type' => 'required:in:' . ConnectionType::Imap->value,
            'email'           => 'required|email|max:191',
            'password'        => $request->has('id') ? 'nullable' : 'required',
            'imap_server'     => 'required|string|max:191',
            'imap_port'       => 'required|numeric',
            'imap_encryption' => ['nullable', Rule::in(ClientManager::ENCRYPTION_TYPES)],
            'smtp_server'     => 'required|string|max:191',
            'smtp_port'       => 'required|numeric',
            'smtp_encryption' => ['nullable', Rule::in(ClientManager::ENCRYPTION_TYPES)],
        ]);

        $validator->after(function ($validator) use ($request) {
            // Validation passes, now we can validate the connections
            $this->testConnection(
                $validator,
                [
                'username'        => $request->input('username'),
                'validate_cert'   => $request->input('validate_cert'),
                'email'           => $request->input('email'),
                'password'        => $this->getPassword($request),
                'imap_server'     => $request->input('imap_server'),
                'imap_port'       => $request->input('imap_port'),
                'imap_encryption' => $request->input('imap_encryption'),
            ],
                [
                'username'        => $request->input('username'),
                'validate_cert'   => $request->input('validate_cert'),
                'email'           => $request->input('email'),
                'password'        => $this->getPassword($request),
                'smtp_server'     => $request->input('smtp_server'),
                'smtp_port'       => $request->input('smtp_port'),
                'smtp_encryption' => $request->input('smtp_encryption'),
            ]
            );
        });

        $validator->validate();

        return $this->response(['folders' => $this->imapFolders]);
    }

    /**
     * Determine which password should be used for the test configuration
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function getPassword(Request $request)
    {
        $account = $request->input('id') ? resolve(EmailAccountRepository::class)
            ->find($request->input('id')) : false;

        if (! $account) {
            return $request->input('password');
        }

        // User inputted password for testing
        if ($password = $request->input('password')) {
            return $password;
        }

        return $account->password;
    }

    /**
     * Test the actual connection after all validation passes
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @param array $imapConfig
     * @param array $smtpConfig
     *
     * @return void
     */
    protected function testConnection(ValidatorContract $validator, array $imapConfig, array $smtpConfig)
    {
        if ($validator->errors()->isEmpty()) {
            try {
                $client = ClientManager::createSmtpClient(new SmtpConfig(
                    $smtpConfig['smtp_server'],
                    $smtpConfig['smtp_port'],
                    $smtpConfig['smtp_encryption'],
                    $smtpConfig['email'],
                    $smtpConfig['validate_cert'],
                    $smtpConfig['username'],
                    $smtpConfig['password']
                ));

                ClientManager::testConnection($client);
            } catch (ConnectionErrorException $e) {
                $validator->errors()->add('smtp-connection', 'SMTP: ' . $e->getMessage());
            }

            try {
                $client = ClientManager::createImapClient(new ImapConfig(
                    $imapConfig['imap_server'],
                    $imapConfig['imap_port'],
                    $imapConfig['imap_encryption'],
                    $imapConfig['email'],
                    $imapConfig['validate_cert'],
                    $imapConfig['username'],
                    $imapConfig['password']
                ));

                ClientManager::testConnection($client);

                $this->imapFolders = $client->getFolders();
            } catch (ConnectionErrorException $e) {
                $validator->errors()->add('imap-connection', 'IMAP: ' . $e->getMessage());
            }
        }
    }
}
