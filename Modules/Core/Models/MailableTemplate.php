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

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Facades\MailableTemplates;
use Modules\Core\Media\HasAttributesWithPendingMedia;

class MailableTemplate extends Model
{
    use HasAttributesWithPendingMedia;

    /**
     * Indicates if the model has timestamps
     *
     * @var bool
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
     * Boot the MailableTemplate model.
     */
    protected static function boot(): void
    {
        parent::boot();

        MailableTemplates::seedIfRequired();
    }

    /**
     * Get the mail template mailable class
     *
     * @return \Modules\Core\MailableTemplate\MailableTemplate
     */
    public function mailable()
    {
        return resolve($this->mailable);
    }

    /**
     * Get mailable template HTMl layout
     */
    public function getHtmlLayout(): ?string
    {
        return null;
    }

    /**
     * Get mailable template text layout
     */
    public function getTextLayout(): ?string
    {
        return null;
    }

    /**
     * Get the mail template placeholders
     *
     * @return \Modules\Core\Placeholders\Collection
     */
    public function getPlaceholders()
    {
        if (! class_exists($this->mailable)) {
            return collect([]);
        }

        $reflection = new \ReflectionClass($this->mailable);
        $mailable = $reflection->newInstanceWithoutConstructor();

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
     */
    public function attributesWithPendingMedia(): string
    {
        return 'html_template';
    }

    /**
     * Scope a query to only include templates of a given locale.
     */
    public function scopeForLocale(Builder $query, string $locale): void
    {
        // @todo
        MailableTemplates::seedIfRequired();

        $query->where('locale', $locale);
    }

    /**
     * Scope a query to only include templates of a given mailable.
     */
    public function scopeForMailable(Builder $query, string $mailable): void
    {
        $query->where('mailable', $mailable);
    }
}
