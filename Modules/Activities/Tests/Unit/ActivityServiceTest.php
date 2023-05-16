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

namespace Modules\Activities\Tests\Unit;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Modules\Activities\Models\Activity;
use Modules\Activities\Models\ActivityType;
use Modules\Activities\Services\ActivityService;
use Modules\Contacts\Models\Contact;
use Modules\Users\Notifications\UserMentioned;
use Modules\Users\Tests\Concerns\TestsMentions;
use Tests\TestCase;

class ActivityServiceTest extends TestCase
{
    use TestsMentions;

    public function test_it_uses_default_activity_type_when_activity_type_is_empty()
    {
        $this->signIn();
        $type = ActivityType::factory()->create();
        ActivityType::setDefault($type->id);

        $attributes = Activity::factory()->raw();
        unset($attributes['activity_type_id']);
        $activity = (new ActivityService())->create($attributes);

        $this->assertEquals($type->id, $activity->activity_type_id);
    }

    public function test_it_can_mark_the_activity_as_completed_on_creation()
    {
        $this->signIn();
        $attributes = Activity::factory()->raw(['is_completed' => true]);

        $activity = (new ActivityService())->create($attributes);

        $this->assertTrue($activity->isCompleted);
    }

    public function test_it_can_mark_the_activity_as_completed_on_update()
    {
        $this->signIn();

        $activity = Activity::factory()->create();

        $activity = (new ActivityService())->update($activity, [
            'is_completed' => true,
        ]);

        $this->assertTrue($activity->isCompleted);
    }

    public function test_it_can_mark_the_activity_as_incompleted_on_update()
    {
        $this->signIn();

        $activity = Activity::factory()->completed()->create();

        $activity = (new ActivityService())->update($activity, [
            'is_completed' => false,
        ]);

        $this->assertFalse($activity->isCompleted);
    }

    public function test_activity_guests_can_be_saved_on_creation()
    {
        $user = $this->signIn();
        $contact = Contact::factory()->create();
        $attributes = Activity::factory()->raw();

        $attributes['guests'] = [
            'users' => [$user->id],
            'contacts' => [$contact->id],
        ];

        $activity = (new ActivityService())->create($attributes);

        $this->assertCount(2, $activity->fresh()->guests);
    }

    public function test_it_send_notifications_to_guests()
    {
        $this->signIn();
        $user = $this->createUser();
        $contact = Contact::factory()->create();
        $attributes = Activity::factory()->raw();
        settings()->set('send_contact_attends_to_activity_mail', true);

        $attributes['guests'] = [
            'users' => [$user->id],
            'contacts' => [$contact->id],
        ];

        Mail::fake();
        Notification::fake();

        (new ActivityService())->create($attributes);

        Notification::assertSentTo($user, $user->getAttendeeNotificationClass());
        Mail::assertQueued($contact->getAttendeeNotificationClass(), function ($mail) use ($contact) {
            return $mail->hasTo($contact->email);
        });
    }

    public function test_it_does_not_send_notification_when_current_user_is_added_as_guest()
    {
        $currentUser = $this->signIn();
        $user = $this->createUser();
        $attributes = Activity::factory()->raw();

        $attributes['guests'] = [
            'users' => [$user->id, $currentUser->id],
        ];

        Notification::fake();

        (new ActivityService())->create($attributes);

        Notification::assertSentTo($user, $user->getAttendeeNotificationClass());
        Notification::assertNotSentTo($currentUser, $user->getAttendeeNotificationClass());
    }

    public function test_it_does_not_send_notification_when_contact_send_notification_is_false()
    {
        $this->signIn();

        $contact = Contact::factory()->create();
        $attributes = Activity::factory()->raw();
        settings()->set('send_contact_attends_to_activity_mail', false);

        $attributes['guests'] = [
            'contacts' => [$contact->id],
        ];

        Mail::fake();

        (new ActivityService())->create($attributes);

        Mail::assertNothingSent();
    }

    public function test_activity_guests_can_be_saved_on_update()
    {
        $user = $this->signIn();
        $contact = Contact::factory()->create();
        $activity = Activity::factory()->create();

        $activity = (new ActivityService())->update($activity, ['guests' => [
            'users' => [$user->id],
            'contacts' => [$contact->id],
        ]]);

        $this->assertSame(2, $activity->guests->count());

        // Detach
        $activity = (new ActivityService())->update($activity, ['guests' => [
            'users' => [$user->id],
        ]]);

        $this->assertSame(1, $activity->guests()->count());
    }

    public function test_it_send_notifications_to_mentioned_users_when_activity_is_created()
    {
        $this->signIn();

        $user = $this->createUser();
        $attributes = Activity::factory()->make([
            'note' => 'Other Text - '.$this->mentionText($user->id, $user->name),
        ])->toArray();

        Notification::fake();

        $activity = (new ActivityService())->create($attributes);

        Notification::assertSentTo($user, UserMentioned::class, function ($notification) use ($activity) {
            return $notification->mentionUrl === "/activities/{$activity->id}";
        });
    }

    public function test_it_send_notifications_to_mentioned_users_when_activity_is_updated()
    {
        $this->signIn();

        $user = $this->createUser();
        $activity = Activity::factory()->create();

        Notification::fake();

        $activity = (new ActivityService())->update($activity, [
            'note' => 'Other Text - '.$this->mentionText($user->id, $user->name),
        ]);

        Notification::assertSentTo($user, UserMentioned::class, function ($notification) use ($activity) {
            return $notification->mentionUrl === "/activities/{$activity->id}";
        });
    }

    public function test_it_send_notifications_to_mentioned_users_when_activity_is_created_via_resource()
    {
        $this->signIn();

        $user = $this->createUser();
        $contact = Contact::factory()->create();
        $attributes = Activity::factory()->make([
            'note' => 'Other Text - '.$this->mentionText($user->id, $user->name),
        ])->toArray();

        Notification::fake();

        $activity = (new ActivityService())->create(array_merge(
            $attributes,
            [
                'via_resource' => 'contacts',
                'via_resource_id' => $contact->id,
            ]
        ));

        Notification::assertSentTo($user, UserMentioned::class, function ($notification) use ($activity, $contact) {
            return $notification->mentionUrl === "/contacts/{$contact->id}?section=activities&resourceId={$activity->id}";
        });
    }

    public function test_it_send_notifications_to_mentioned_users_when_activity_is_updated_via_resource()
    {
        $this->signIn();

        $user = $this->createUser();
        $contact = Contact::factory()->create();
        $activity = Activity::factory()->create();

        Notification::fake();

        $activity = (new ActivityService())->update($activity, [
            'note' => 'Other Text - '.$this->mentionText($user->id, $user->name),
            'via_resource' => 'contacts',
            'via_resource_id' => $contact->id,
        ]);

        Notification::assertSentTo($user, UserMentioned::class, function ($notification) use ($activity, $contact) {
            return $notification->mentionUrl === "/contacts/{$contact->id}?section=activities&resourceId={$activity->id}";
        });
    }
}
