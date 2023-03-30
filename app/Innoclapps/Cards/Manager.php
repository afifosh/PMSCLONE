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

namespace App\Innoclapps\Cards;

use Illuminate\Support\Collection;

class Manager
{
    /**
     * All registered resources cards
     *
     * @var array
     */
    protected array $cards = [];

    /**
     * Register resource cards
     *
     * @param string $resource
     * @param mixed $provider
     *
     * @return static
     */
    public function register(string $resource, mixed $provider) : static
    {
        if (isset($this->cards[$resource])) {
            $this->cards[$resource]['providers'][] = $provider;
        } else {
            $this->cards[$resource] = ['providers' => [$provider]];
        }

        return $this;
    }

    /**
     * Resolves cards for a given resource
     *
     * @param string $resourceName
     *
     * @return \Illuminate\Support\Collection
     */
    public function resolve(string $resourceName) : Collection
    {
        return $this->forResource($resourceName)->filter->authorizedToSee()
            ->reject(fn ($card) => $card->onlyOnDashboard === true)
            ->values();
    }

    /**
     * Resolve cards for dashboard
     *
     * @return \Illuminate\Support\Collection
     */
    public function resolveForDashboard() : Collection
    {
        return $this->registered()->filter->authorizedToSee()
            ->reject(fn ($card) => $card->onlyOnIndex === true)
            ->values();
    }

    /**
     * Get all registered cards for a given resource
     *
     * @param string $resourceName
     *
     * @return \Illuminate\Support\Collection
     */
    public function forResource(string $resourceName) : Collection
    {
        return $this->load($this->cards[$resourceName]);
    }

    /**
     * Get all registerd application cards
     *
     * @return \Illuminate\Support\Collection
     */
    public function registered() : Collection
    {
        return (new Collection)->whenEmpty(function ($collection) {
            foreach (array_keys($this->cards) as $resourceName) {
                $collection = $collection->merge(
                    $this->forResource($resourceName)
                );
            }

            return $collection;
        });
    }

    /**
     * Load the provided cards.
     *
     * @param array $data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function load(array $data) : Collection
    {
        $cards     = new Collection;
        $providers = $data['providers'];

        foreach ($providers as $provider) {
            if ($provider instanceof Card) {
                $provider = [$provider];
            }

            if (is_array($provider)) {
                $cards = $cards->merge($provider);
            } elseif (is_callable($provider)) {
                $cards = $cards->merge(call_user_func($provider));
            }
        }

        return $cards;
    }
}
