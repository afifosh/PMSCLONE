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

namespace Modules\Contacts\Fields;

use Modules\Contacts\Http\Resources\ContactResource;
use Modules\Contacts\Models\Contact as ContactModel;
use Modules\Core\Fields\BelongsTo;

class Contact extends BelongsTo
{
    /**
     * Create new instance of Contact field
     *
     * @param  string  $relationName The relation name, snake case format
     * @param  string  $label Custom label
     * @param  string  $foreignKey Custom foreign key
     */
    public function __construct($relationName = 'contact', $label = null, $foreignKey = null)
    {
        parent::__construct($relationName, ContactModel::class, $label ?? __('contacts::contact.contact'), $foreignKey);

        $this->labelKey('display_name')
            ->setJsonResource(ContactResource::class)
            ->async('/contacts/search')
            ->provideSampleValueUsing(fn () => [1, 2]);
    }
}
