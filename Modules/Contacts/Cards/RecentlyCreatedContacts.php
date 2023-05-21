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

namespace Modules\Contacts\Cards;

use Modules\Contacts\Criteria\ViewAuthorizedContactsCriteria;
use Modules\Contacts\Models\Contact;
use Modules\Core\Card\TableCard;
use Modules\Core\Date\Carbon;

class RecentlyCreatedContacts extends TableCard
{
    /**
     * Limit the number of records shown in the table
     *
     * @var int
     */
    protected $limit = 20;

    /**
     * Created in the last 30 days
     *
     * @var int
     */
    protected $days = 30;

    /**
     * Provide the table items
     *
     * @return \Illuminate\Support\Collection
     */
    public function items(): iterable
    {
        return Contact::select(['id', 'first_name', 'last_name', 'created_at', 'email'])
            ->criteria(ViewAuthorizedContactsCriteria::class)
            ->where('created_at', '>', Carbon::asCurrentTimezone()->subDays($this->days)->inAppTimezone())
            ->latest()
            ->limit($this->limit)
            ->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'display_name' => $contact->display_name,
                    'email' => $contact->email,
                    'created_at' => $contact->created_at,
                    'path' => $contact->path,
                ];
            });
    }

    /**
     * Provide the table fields
     */
    public function fields(): array
    {
        return [
            ['key' => 'display_name', 'label' => __('contacts::contact.contact')],
            ['key' => 'email', 'label' => __('contacts::fields.contacts.email')],
            ['key' => 'created_at', 'label' => __('core::app.created_at')],
        ];
    }

    /**
     * Card title
     */
    public function name(): string
    {
        return __('contacts::contact.cards.recently_created');
    }

    /**
     * jsonSerialize
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'help' => __('contacts::contact.cards.recently_created_info', ['total' => $this->limit, 'days' => $this->days]),
        ]);
    }
}
