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

namespace App\Innoclapps\MailableTemplates;

use Illuminate\Support\Str;
use App\Innoclapps\Html2Text;
use Illuminate\Mail\Mailable;
use Illuminate\Support\HtmlString;
use App\Innoclapps\Contracts\Repositories\MailableRepository;

abstract class MailableTemplate extends Mailable
{
    /**
     * Holds the template model
     *
     * @var \App\Innoclapps\Models\MailableTemplate
     */
    protected $templateModel;

    /**
     * Provides the default mail template content
     *
     * e.q. is used when seeding the mail templates
     *
     * @return \App\Innoclapps\MailableTemplates\DefaultMailable
     */
    abstract public static function default() : DefaultMailable;

    /**
     * Get the mailable human readable name
     *
     * @return string
     */
    public static function name()
    {
        return Str::title(Str::snake(class_basename(get_called_class()), ' '));
    }

    /**
     * Build the view for the message.
     *
     * @return array
     */
    protected function buildView()
    {
        $renderer = $this->getMailableTemplateRenderer();

        return array_filter([
            'html' => new HtmlString($renderer->renderHtmlLayout()),
            'text' => new HtmlString($renderer->renderTextLayout()),
        ]);
    }

    /**
     * Build the view data for the message.
     *
     * @return array
     */
    public function buildViewData()
    {
        return $this->placeholders()?->parse() ?: parent::buildViewData();
    }

    /**
     * Build the subject for the message.
     *
     * @param \Illuminate\Mail\Message|\App\Innoclapps\MailClient\Client $buildable
     *
     * @return static
     */
    protected function buildSubject($buildable)
    {
        $buildable->subject($this->getMailableTemplateRenderer()->renderSubject());

        return $this;
    }

    /**
     * Get the mailable template subject
     *
     * @return string|null
     */
    protected function getMailableTemplateSubject()
    {
        return $this->subject ?? $this->getMailableTemplate()->getSubject() ?? $this->name();
    }

    /**
     * Get the mailable template model
     *
     * @return \App\Innoclapps\Models\MailableTemplate
     */
    public function getMailableTemplate()
    {
        return $this->templateModel ?? $this->resolveMailableTemplateModel();
    }

    /**
     * Resolve the mailable template model
     *
     * @return \App\Innoclapps\Models\MailableTemplate
     */
    protected function resolveMailableTemplateModel()
    {
        return $this->templateModel = static::templateRepository()->forMailable(
            $this,
            $this->locale ?? 'en'
        );
    }

    /**
     * Get the mail template repository
     *
     * @return \App\Innoclapps\Contracts\Repositories\MailableRepository
     */
    protected static function templateRepository()
    {
        return app(MailableRepository::class);
    }

    /**
     * Creates alternative text message from the given HTML
     *
     * @param string $html
     *
     * @return string
     */
    protected static function altMessageFromHtml($html)
    {
        return Html2Text::convert($html);
    }

    /**
     * Get the mail template content rendered
     *
     * @return \App\Innoclapps\MailableTemplates\Renderer
     */
    protected function getMailableTemplateRenderer() : Renderer
    {
        $template = $this->getMailableTemplate();

        return app(Renderer::class, [
            'htmlTemplate' => $template->getHtmlTemplate(),
            'subject'      => $this->getMailableTemplateSubject(),
            'placeholders' => $this->placeholders(),
            'htmlLayout'   => $this->getHtmlLayout(),
            'textTemplate' => $template->getTextTemplate() ?: static::altMessageFromHtml($template->getHtmlTemplate()),
            'textLayout'   => $this->getTextLayout(),
        ]);
    }

    /**
     * Get the mailable HTML layout
     *
     * @return string|null
     */
    public function getHtmlLayout()
    {
        $default = config('innoclapps.mailables.layout');

        if (file_exists($default)) {
            return file_get_contents($default);
        }
    }

    /**
     * Get the mailable text layout
     *
     * @return string|null
     */
    public function getTextLayout()
    {
        //
    }

    /**
     * Provide the defined mailable template placeholders
     *
     * @return \App\Innoclapps\Placeholders\Collection|null
     */
    public function placeholders()
    {
        //
    }

    /**
     * The Mailable build method
     *
     * @see  buildSubject, buildView, send
     *
     * @return static
     */
    public function build()
    {
        return $this;
    }

    /**
     * Seed the mailable in database as mail template
     *
     * @param string $locale Locale to seed the mail template
     *
     * @return \App\Innoclapps\Models\MailableTemplate
     */
    public static function seed($locale = 'en')
    {
        $default    = static::default();
        $mailable   = get_called_class();
        $repository = static::templateRepository();

        $template = $repository->firstOrNew(
            [
                'locale'   => $locale,
                'mailable' => $mailable,
            ],
            [
                 'locale'        => $locale,
                 'subject'       => $default->subject(),
                 'html_template' => $default->htmlMessage(),
                 'text_template' => $default->textMessage(),
            ]
        );

        if (! $template->exists) {
            $template->forceFill(['mailable' => $mailable, 'name' => static::name()])->save();
        }

        return $template;
    }
}
