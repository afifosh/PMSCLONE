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

namespace Modules\Core\Tests\Feature\Resource;

use Modules\Contacts\Models\Contact;
use Tests\TestCase;

class PlaceholdersControllerTest extends TestCase
{
    public function test_unauthenticated_user_cannot_access_the_placeholders_endpoints()
    {
        $this->getJson('/api/placeholders')->assertUnauthorized();
        $this->postJson('/api/placeholders')->assertUnauthorized();
    }

    public function test_placeholders_can_be_retrieved()
    {
        $this->signIn();

        $this->getJson('/api/placeholders?'.http_build_query(['resources' => ['contacts']]))->assertJsonStructure([
            'contacts' => ['label', 'placeholders' => [
                0 => [
                    'tag', 'description', 'interpolation_start', 'interpolation_end',
                ],
            ],
            ],
        ]);
    }

    public function test_placeholders_can_be_parsed()
    {
        $this->signIn();
        $contact = Contact::factory()->create();

        $response = $this->postJson('/api/placeholders', [
            'content' => '<input class="_placeholder" type="text" value="" placeholder="E-Mail Address" data-group="contacts" data-tag="email" /><input class="_placeholder" type="text" value="" placeholder="First Name" data-group="contacts" data-tag="first_name" />',
            'resources' => [['name' => 'contacts', 'id' => $contact->id]],
        ]);

        $expected = '<input class="_placeholder" type="text" value="'.$contact->email.'" placeholder="E-Mail Address" data-group="contacts" data-tag="email" data-autofilled /><input class="_placeholder" type="text" value="'.$contact->first_name.'" placeholder="First Name" data-group="contacts" data-tag="first_name" data-autofilled />';

        $this->assertEquals($expected, json_decode($response->getContent()));
    }

    public function test_it_does_not_parse_placeholders_if_the_user_is_not_authorized_to_view_the_resource_record()
    {
        $this->asRegularUser()->signIn();
        $otherUser = $this->createUser();
        $contact = Contact::factory()->for($otherUser)->create();

        $response = $this->postJson('/api/placeholders', [
            'content' => '<input class="_placeholder" type="text" value="" placeholder="E-Mail Address" data-group="contacts" data-tag="email" />',
            'resources' => [['name' => 'contacts', 'id' => $contact->id]],
        ]);

        $this->assertEquals('<input class="_placeholder" type="text" value="" placeholder="E-Mail Address" data-group="contacts" data-tag="email" />', json_decode($response->getContent()));
    }
}
