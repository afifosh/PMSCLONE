<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Tests\Unit;

use Illuminate\Support\Facades\Crypt;
use Modules\Core\Models\OAuthAccount;
use Modules\Core\Synchronization\SyncState;
use Modules\MailClient\Client\Client;
use Modules\MailClient\Client\Gmail\ImapClient as GmailImapClient;
use Modules\MailClient\Client\Gmail\SmtpClient as GmailSmtpClient;
use Modules\MailClient\Client\Imap\Config as ImapConfig;
use Modules\MailClient\Client\Imap\ImapClient;
use Modules\MailClient\Client\Imap\SmtpClient;
use Modules\MailClient\Client\Imap\SmtpConfig;
use Modules\MailClient\Client\Outlook\ImapClient as OutlookImapClient;
use Modules\MailClient\Client\Outlook\SmtpClient as OutlookSmtpClient;
use Modules\MailClient\Enums\EmailAccountType;
use Modules\MailClient\Models\EmailAccount;
use Modules\MailClient\Models\EmailAccountFolder;
use Modules\MailClient\Services\EmailAccountService;
// use Modules\Users\Models\User;
use App\Models\Admin as User;
use Tests\TestCase;

class EmailAccountModelTest extends TestCase
{
    public function test_account_has_oauth_account()
    {
        $account = EmailAccount::factory()->create([
            'access_token_id' => OAuthAccount::factory()->create()->id,
        ]);

        $this->assertInstanceOf(OAuthAccount::class, $account->oAuthAccount);
    }

    public function test_account_has_folders()
    {
        $account = EmailAccount::factory()->has(EmailAccountFolder::factory()->count(2), 'folders')->create();

        $this->assertCount(2, $account->folders);
    }

    public function test_account_has_sent_folder()
    {
        $account = EmailAccount::factory()->for(EmailAccountFolder::factory(), 'sentFolder')->create();

        $this->assertInstanceOf(EmailAccountFolder::class, $account->sentFolder);
    }

    public function test_account_has_trash_folder()
    {
        $account = EmailAccount::factory()->for(EmailAccountFolder::factory(), 'trashFolder')->create();

        $this->assertInstanceOf(EmailAccountFolder::class, $account->trashFolder);
    }

    public function test_it_can_determine_whether_sync_is_disabled_for_the_account()
    {
        $this->assertTrue(EmailAccount::factory()->syncDisabled()->make()->isSyncDisabled());
        $this->assertFalse(EmailAccount::factory()->make()->isSyncDisabled());
    }

    public function test_it_can_determine_whether_sync_is_stopped_for_the_account()
    {
        $this->assertTrue(EmailAccount::factory()->syncStopped()->make()->isSyncStoppedBySystem());
        $this->assertFalse(EmailAccount::factory()->make()->isSyncStoppedBySystem());
    }

    public function test_it_can_determine_whether_initial_sync_is_performed()
    {
        $this->assertFalse(EmailAccount::factory()->make(['last_sync_at' => null])->isInitialSyncPerformed());
        $this->assertTrue(EmailAccount::factory()->make(['last_sync_at' => now()])->isInitialSyncPerformed());
    }

    public function test_it_can_determine_whether_account_requires_authentication()
    {
        $this->assertTrue(EmailAccount::factory()->requiresAuth()->make()->requires_auth);
        $this->assertFalse(EmailAccount::factory()->make(['requires_auth' => false])->requires_auth);
    }

    public function test_it_can_determine_whether_requires_authentication_via_oauth_account()
    {
        $account = EmailAccount::factory()->make([
            'access_token_id' => OAuthAccount::factory()->requiresAuth()->create()->id,
        ]);

        $this->assertTrue($account->requires_auth);

        $account = EmailAccount::factory()->make([
            'access_token_id' => OAuthAccount::factory()->create()->id,
        ]);

        $this->assertFalse($account->requires_auth);
    }

    public function test_account_can_be_personal()
    {
        $account = EmailAccount::factory()->personal()->make();

        $this->assertInstanceOf(User::class, $account->user);
        $this->assertTrue($account->isPersonal());
        $this->assertFalse($account->isShared());
    }

    public function test_account_can_be_shared()
    {
        $account = EmailAccount::factory()->shared()->make();

        $this->assertNull($account->user);
        $this->assertTrue($account->isShared());
        $this->assertFalse($account->isPersonal());
    }

    public function test_account_has_type()
    {
        $this->assertEquals(
            EmailAccountType::PERSONAL,
            EmailAccount::factory()->personal()->make()->type
        );

        $this->assertEquals(
            EmailAccountType::SHARED,
            EmailAccount::factory()->shared()->make()->type
        );
    }

    public function test_cant_send_mails_when_requires_authentication()
    {
        $account = EmailAccount::factory()->requiresAuth()->make();

        $this->assertFalse($account->canSendMails());
    }

    public function test_cant_send_mails_when_sync_is_stopped_by_system()
    {
        $account = EmailAccount::factory()->syncStopped()->make();

        $this->assertFalse($account->canSendMails());
    }

    public function test_can_send_emails_when_it_does_not_requires_auth_and_sync_is_not_stopped_by_system()
    {
        $account = EmailAccount::factory()->make();

        $this->assertTrue($account->canSendMails());
    }

    public function test_imap_account_has_mail_client()
    {
        $account = EmailAccount::factory()->imap()->create();

        $client = $account->getClient();

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ImapClient::class, $client->getImap());
        $this->assertInstanceOf(SmtpClient::class, $client->getSmtp());
    }

    public function test_gmail_account_has_mail_client()
    {
        $account = EmailAccount::factory()->gmail()->create([
            'access_token_id' => OAuthAccount::factory()->create()->id,
        ]);

        $client = $account->getClient();

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(GmailImapClient::class, $client->getImap());
        $this->assertInstanceOf(GmailSmtpClient::class, $client->getSmtp());
    }

    public function test_outlook_account_has_mail_client()
    {
        $account = EmailAccount::factory()->outlook()->create([
            'access_token_id' => OAuthAccount::factory()->create()->id,
        ]);

        $client = $account->getClient();

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(OutlookImapClient::class, $client->getImap());
        $this->assertInstanceOf(OutlookSmtpClient::class, $client->getSmtp());
    }

    public function test_it_uses_the_from_name_header_when_shared()
    {
        $account = EmailAccount::factory()->shared()->create();
        $account->setMeta('from_name_header', 'Custom From Name Header');

        $this->assertSame('Custom From Name Header', $account->from_name_header);
    }

    public function test_account_has_formatted_from_name_header()
    {
        $user = $this->signIn();

        $account = EmailAccount::factory()->shared()->create();
        $account->setMeta('from_name_header', '{agent} from {company}');

        $company = config('app.name');
        $this->assertSame("{$user->name} from {$company}", $account->formatted_from_name_header);
    }

    public function test_formatted_from_name_header_attribute_does_not_throw_errors_when_the_user_is_not_logged_in()
    {
        $account = EmailAccount::factory()->shared()->create();
        $account->setMeta('from_name_header', '{agent} from {company}');

        $company = config('app.name');
        $this->assertSame(" from {$company}", $account->formatted_from_name_header);
    }

    public function test_account_can_be_primary()
    {
        $user = $this->signIn();
        $account = EmailAccount::factory()->create();
        $account->markAsPrimary($user);

        $this->assertTrue($account->isPrimary());
    }

    public function test_account_can_be_unmarked_as_primary()
    {
        $user = $this->signIn();
        $account = EmailAccount::factory()->create();
        $account->markAsPrimary($user);

        EmailAccount::unmarkAsPrimary($user);

        $this->assertFalse($account->isPrimary());
    }

    public function test_account_can_be_primary_to_multiple_users()
    {
        $account = EmailAccount::factory()->create();

        $user1 = $this->createUser();
        $account->markAsPrimary($user1);

        $user2 = $this->createUser();
        $account->markAsPrimary($user2);

        $this->signIn($user1);
        $this->assertTrue($account->isPrimary());

        $this->signIn($user2);
        $this->assertTrue($account->isPrimary());
    }

    public function test_it_encrypts_imap_account_password()
    {
        Crypt::shouldReceive('encrypt')->once()
            ->with('password', false)
            ->andReturnArg(0);

        new EmailAccount(['password' => 'password']);
    }

    public function test_it_decrypts_imap_account_password()
    {
        $account = new EmailAccount(['password' => 'password']);

        Crypt::shouldReceive('decrypt')->once()
            ->andReturn('password');

        $this->assertEquals('password', $account->password);
    }

    public function test_imap_account_has_smtp_config()
    {
        $account = EmailAccount::factory()->imap([
            'password' => 'test',
            'smtp_server' => 'smtp.example.com',
            'smtp_port' => 993,
            'smtp_encryption' => 'ssl',
            'validate_cert' => false,
            'username' => 'smtp-username',
        ])->create(['email' => 'smtp@example.com']);

        $config = $account->getSmtpConfig();

        $this->assertInstanceOf(SmtpConfig::class, $config);

        $this->assertSame('test', $config->password());
        $this->assertSame('smtp@example.com', $config->email());
        $this->assertSame('smtp.example.com', $config->host());
        $this->assertSame(993, $config->port());
        $this->assertSame('ssl', $config->encryption());
        $this->assertSame('smtp-username', $config->username());
        $this->assertFalse($config->validateCertificate());
    }

    public function test_imap_account_has_imap_config()
    {
        $account = EmailAccount::factory()->imap([
            'password' => 'test',
            'imap_server' => 'imap.example.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'validate_cert' => true,
            'username' => 'imap-username',
        ])->create(['email' => 'imap@example.com']);

        $config = $account->getImapConfig();

        $this->assertInstanceOf(ImapConfig::class, $config);

        $this->assertSame('test', $config->password());
        $this->assertSame('imap@example.com', $config->email());
        $this->assertSame('imap.example.com', $config->host());
        $this->assertSame(993, $config->port());
        $this->assertSame('ssl', $config->encryption());
        $this->assertSame('imap-username', $config->username());
        $this->assertTrue($config->validateCertificate());
    }

    public function test_when_from_name_header_is_empty_it_uses_the_default()
    {
        $this->signIn();
        $payload = EmailAccount::factory()->shared()->raw();

        $account = (new EmailAccountService())->create(array_merge($payload, [
            'from_name_header' => '',
        ]));

        $this->assertSame(EmailAccount::DEFAULT_FROM_NAME_HEADER, $account->from_name_header);
    }

    public function test_when_from_name_header_is_null_it_uses_the_default()
    {
        $this->signIn();
        $payload = EmailAccount::factory()->shared()->raw();

        $account = (new EmailAccountService())->create(array_merge($payload, [
            'from_name_header' => null,
        ]));

        $this->assertSame(EmailAccount::DEFAULT_FROM_NAME_HEADER, $account->from_name_header);
    }

    public function test_it_can_mark_account_as_primary()
    {
        $user = $this->signIn();
        $account = EmailAccount::factory()->create();

        $account->markAsPrimary($user);

        $this->assertTrue($account->isPrimary());
    }

    public function test_it_can_unmark_account_as_primary()
    {
        $user = $this->signIn();
        $account = EmailAccount::factory()->create();

        $account->markAsPrimary($user);

        EmailAccount::unmarkAsPrimary($user);

        $this->assertFalse($account->isPrimary());
    }

    public function test_it_can_set_that_account_requires_authentication()
    {
        $account = EmailAccount::factory()->create();

        $account->setRequiresAuthentication($account->id);

        $this->assertTrue($account->requires_auth);
    }

    public function test_it_sets_requires_authentication_on_the_auth_account()
    {
        $account = EmailAccount::factory()->create([
            'access_token_id' => OAuthAccount::factory()->create()->id,
        ]);

        $account->setRequiresAuthentication($account->id);

        $this->assertTrue($account->requires_auth);
        $this->assertTrue($account->oAuthAccount->requires_auth);
    }

    public function test_it_can_set_that_account_doesnt_requires_authentication()
    {
        $account = EmailAccount::factory()->requiresAuth()->create();

        $account->setRequiresAuthentication(false);

        $this->assertFalse($account->requires_auth);
    }

    public function test_it_sents_doesnt_requires_authentication_on_the_oauth_account()
    {
        $account = EmailAccount::factory()->create([
            'access_token_id' => OAuthAccount::factory()->requiresAuth()->create()->id,
        ]);

        $account->setRequiresAuthentication(false);

        $this->assertFalse($account->requires_auth);
        $this->assertFalse($account->oAuthAccount->requires_auth);
    }

    public function test_personal_email_accounts_can_be_retrieved()
    {
        $user = $this->createUser();
        $personal = EmailAccount::factory()->personal($user)->create();
        EmailAccount::factory()->shared()->create();

        $accounts = EmailAccount::personal($user->id)->get();

        $this->assertCount(1, $accounts);
        $this->assertSame($personal->email, $accounts[0]->email);
    }

    public function test_shared_email_accounts_can_be_retrieved()
    {
        EmailAccount::factory()->personal()->create();
        $shared = EmailAccount::factory()->shared()->create();

        $accounts = EmailAccount::shared()->get();

        $this->assertCount(1, $accounts);
        $this->assertSame($shared->email, $accounts[0]->email);
    }

    public function test_syncable_email_accounts_can_be_retrieved()
    {
        EmailAccount::factory()->syncDisabled()->create();
        EmailAccount::factory()->syncStopped()->create();
        EmailAccount::factory()->create();
        EmailAccount::factory()->create(['sync_state' => SyncState::ENABLED]);

        $this->assertSame(2, EmailAccount::syncable()->count());
    }

    public function test_it_can_enable_sync_for_account()
    {
        $account = EmailAccount::factory()->syncDisabled()->create();

        $account->enableSync();

        $this->assertFalse($account->isSyncDisabled());
    }

    public function test_it_can_set_sync_state_for_account()
    {
        $account = EmailAccount::factory()->create();

        $account->setSyncState(SyncState::DISABLED, 'Disabled via tests.');

        $this->assertSame('Disabled via tests.', $account->sync_state_comment);
        $this->assertSame(SyncState::DISABLED, $account->sync_state);
    }
}
