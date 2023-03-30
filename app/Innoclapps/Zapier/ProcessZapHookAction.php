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

namespace App\Innoclapps\Zapier;

use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Innoclapps\Contracts\Repositories\ZapierHookRepository;

class ProcessZapHookAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The code that Zapier throws when we need to unsubscribe from the hook.
     */
    const STATUS_CODE_UNSUBSCRIBE = 410;

    /**
     * Chunk size for the payload
     *
     * @var integer
     */
    const CHUNK_SIZE = 50;

    /**
    * The number of times the job may be attempted.
    *
    * @var int
    */
    public $tries = 1;

    /**
     * Create new ProcessZapHooksAction instance
     *
     * @param string $hookUrl
     * @param mixed $payload
     */
    public function __construct(protected string $hookUrl, protected $payload)
    {
    }

    /**
     * Execute the job.
     *
     * @param \App\Innoclapps\Contracts\Repositories\ZapierHookRepository $repository
     *
     * @return void
     */
    public function handle(ZapierHookRepository $repository)
    {
        collect(Arr::wrap($this->payload))->chunk(static::CHUNK_SIZE)
            ->each(function ($data) use ($repository) {
                $response = Http::post($this->hookUrl, $data->all());

                if ($response->clientError() &&
                            $response->status() === static::STATUS_CODE_UNSUBSCRIBE) {
                    // Remove failed hook
                    $repository->deleteByUrl($this->hookUrl);
                } else {
                    $response->throw();
                }
            });
    }
}
