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

namespace App\Innoclapps\Models;

use App\Support\Concerns\HasAttributesWithPendingMedia;

class MailableTemplate extends Model
{
    use HasAttributesWithPendingMedia;

    /**
     * Indicates if the model has timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'html_template', 'text_template', 'locale',
    ];

    /**
     * Get the mail template mailable class
     *
     * @return \App\Innoclapps\MailableTemplates\MailableTemplate
     */
    public function mailable()
    {
        return resolve($this->mailable);
    }

    /**
     * Get mailable template HTMl layout
     *
     * @return string|null
     */
    public function getHtmlLayout()
    {
        return null;
    }

    /**
     * Get mailable template text layout
     *
     * @return string|null
     */
    public function getTextLayout()
    {
        return null;
    }

    /**
     * Get the mail template placeholders
     *
     * @return \App\Innoclapps\Placeholders\Collection
     */
    public function getPlaceholders()
    {
        if (! class_exists($this->mailable)) {
            return collect([]);
        }

        $reflection = new \ReflectionClass($this->mailable);
        $mailable   = $reflection->newInstanceWithoutConstructor();

        return $mailable->placeholders() ?: collect([]);
    }

    /**
     * Get mail template subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get html template
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->html_template;
    }

    /**
     * Get text template
     *
     * @return string
     */
    public function getTextTemplate()
    {
        return $this->text_template;
    }

    /**
     * Get the attributes that may contain pending media
     *
     * @return string
     */
    public function attributesWithPendingMedia() : string
    {
        return 'html_template';
    }
}