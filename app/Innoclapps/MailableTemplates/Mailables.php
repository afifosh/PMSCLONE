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

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use App\Innoclapps\Translation\Translation;
use App\Innoclapps\Contracts\Repositories\MailableRepository;

class Mailables
{
    /**
     * Custom registered mailables
     *
     * @var array
     */
    protected $mailables = [];

    /**
     * Database templates cache
     *
     * @var null|\Illuminate\Support\Collection
     */
    protected $dbTemplates;

    /**
     * Collected mailable templates cache
     *
     * @var null|\Illuminate\Support\Collection
     */
    protected $collectedMailables;

    /**
     * Indicates whether templates should be auto discoeverd
     *
     * @var boolean
     */
    protected static $autoDiscovery = true;

    /**
     * Initialize new Mailables instnace.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param string|null $path
     * @param string $namespace
     */
    public function __construct(protected Filesystem $filesystem, string $path = null, protected $namespace = 'App\\Mail\\')
    {
        $this->path = $path ?: app_path('Mail');
    }

    /**
     * Register new mailable class
     *
     * @param string $mailable
     *
     * @return static
     */
    public function register(string $mailable) : static
    {
        $this->mailables[] = $mailable;

        return $this;
    }

    /**
     * Set that the template won't be auto discovered
     *
     * @return void
     */
    public static function dontDiscover() : void
    {
        static::autoDiscovery(false);
    }

    /**
     * Set auto discovery
     *
     * @param bool $value
     *
     * @return void
     */
    public static function autoDiscovery($value) : void
    {
        static::$autoDiscovery = $value;
    }

    /**
     * Get all available/registered mailables
     *
     * @return \Illuminate\Support\Collection
     */
    public function get() : Collection
    {
        $mailables = $this->collectMailables();

        return $mailables->filter(function ($mailable) {
            $mailable = new ReflectionClass($mailable);

            if ($mailable->isAbstract()) {
                return false;
            }

            return $mailable->isSubclassOf(MailableTemplate::class);
        })->values();
    }

    /**
     * Seed mailable(s)
     *
     * @param string $locale
     * @param string|null $mailable The mailable class name to seed
     *
     * If $mailable is not passed will try to seed all mailables
     *
     * @return void
     */
    public function seed(string $locale, string $mailable = null) : void
    {
        $mailables = $mailable ? [$mailable] : $this->get();

        foreach ($mailables as $mailable) {
            $mailable = new ReflectionMethod($mailable, 'seed');

            $mailable->invoke(null, $locale);
        }
    }

    /**
     * Check whether mailables requires seeding
     *
     * @return boolean
     */
    public function requiresSeeding() : bool
    {
        $dbTemplates    = $this->getMailableTemplates();
        $totalAvailable = $this->get()->count();

        foreach (Translation::availableLocales() as $locale) {
            if ($dbTemplates->where('locale', $locale)->count() < $totalAvailable) {
                return true;
            }
        }

        // Check if the template in local does not exists
        return ! is_null($this->getHangingDatabaseTemplates());
    }

    /**
     * Get the database mailable templates
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMailableTemplates() : Collection
    {
        if (! $this->dbTemplates) {
            $this->dbTemplates = resolve(MailableRepository::class)->all();
        }

        return $this->dbTemplates;
    }

    /**
     * Get the database templates that are without local template
     * In this case, the local file template is deleted but the one in database is still hanging there
     *
     * @return \Illuminate\Support\Collection|null
     */
    protected function getHangingDatabaseTemplates() : ?Collection
    {
        $local = $this->get();

        $dbMailables = $this->getMailableTemplates()->unique('mailable');

        $removed = array_diff($dbMailables->pluck('mailable')->all(), $local->all());

        if (count($removed) > 0) {
            return $dbMailables->filter(function ($template) use ($removed) {
                return in_array($template->mailable, $removed);
            })->values();
        }

        return null;
    }

    /**
     * Check whether the mailables requires seeding
     * If requires seeding will seed them in database
     *
     * @return void
     */
    public function seedIfRequired() : void
    {
        if ($this->requiresSeeding()) {
            if ($fileDeleted = $this->getHangingDatabaseTemplates()) {
                $repository = resolve(MailableRepository::class);

                $fileDeleted->each(function ($template) use ($repository) {
                    $repository->delete($template->getKey());
                });
            }

            $locales = Translation::availableLocales();

            foreach ($locales as $locale) {
                $this->seed($locale);
            }
        }
    }

    /**
     * Clean the cached mailables
     *
     * @return static
     */
    public function flushCache() : static
    {
        $this->collectedMailables = null;

        return $this;
    }

    /**
     * Collect and get all the available mailables
     *
     * Custom registered and auto discovered
     *
     * @return \Illuminate\Support\Collection
     */
    protected function collectMailables() : Collection
    {
        if ($this->collectedMailables) {
            return $this->collectedMailables;
        }

        $discovered = [];
        if (static::$autoDiscovery === true) {
            $mailableTemplates = $this->filesystem->files($this->path);

            foreach ($mailableTemplates as $file) {
                $discovered[] = $this->namespace . $file->getFilenameWithoutExtension();
            }
        }

        return $this->collectedMailables = collect(array_merge($discovered, $this->mailables));
    }
}
