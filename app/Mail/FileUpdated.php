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

namespace App\Mail;

use App\Models\User;
use App\Innoclapps\Placeholders\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Innoclapps\Placeholders\UrlPlaceholder;
use App\Innoclapps\Placeholders\UserPlaceholder;
use App\Innoclapps\MailableTemplates\DefaultMailable;
use App\Support\MailPlaceholders\ActionButtonPlaceholder;
use App\Support\MailPlaceholders\PrivacyPolicyPlaceholder;

class FileUpdated extends MailableTemplate implements ShouldQueue
{
    /**
     * Create a new mailable template instance.
     *
     * @param \App\Models\User $mentioned
     * @param string $mentionUrl
     * @param \App\Models\User $mentioner
     *
     * @return void
     */
    public function __construct(protected User $mentioned, protected string $mentionUrl, protected User $mentioner)
    {
    }

    /**
     * Provide the defined mailable template placeholders
     *
     * @return \App\Innoclapps\Placeholders\Collection
     */
    public function placeholders()
    {
        return new Collection([
                UserPlaceholder::make(fn () => $this->mentioned->name)
                    ->tag('mentioned_user')
                    ->description(__('mail_template.placeholders.mentioned_user')),

                UserPlaceholder::make(fn () => $this->mentioner->name)
                    ->description(__('mail_template.placeholders.user_that_mentions')),

            UrlPlaceholder::make(fn () => $this->mentionUrl)
                ->description(__('mail_template.placeholders.mention_url')),

            ActionButtonPlaceholder::make(fn () => $this->mentionUrl),

            PrivacyPolicyPlaceholder::make(),
        ]);
    }

    /**
     * Provides the mail template default configuration
     *
     * @return \App\Innoclapps\MailableTemplates\DefaultMailable
     */
    public static function default() : DefaultMailable
    {
        return new DefaultMailable(static::defaultHtmlTemplate(), static::defaultSubject());
    }

    /**
     * Provides the mail template default message
     *
     * @return string
     */
    public static function defaultHtmlTemplate()
    {
        return '<p>Hello {{ mentioned_user }}<br /></p>
                <p>{{ user }} mentioned you.<br /></p>
                <p>{{{ action_button }}}<br /></p>';
    }

    /**
     * Provides the mail template default subject
     *
     * @return string
     */
    public static function defaultSubject()
    {
        return 'You Were Mentioned by {{ user }}';
    }
}
