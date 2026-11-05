<?php

namespace Tests\Feature\Admin;

use App\Enums\MessageState;
use App\Enums\VolunteerStatus;
use App\Models\ContactMessage;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_a_message_marks_it_read(): void
    {
        $message = ContactMessage::factory()->create();

        $this->actingAs(User::factory()->create())->get(route('admin.messages.show', $message))->assertOk();

        $this->assertSame(MessageState::Read, $message->fresh()->state());
    }

    public function test_message_can_be_archived_and_restored(): void
    {
        $message = ContactMessage::factory()->read()->create();
        $this->actingAs(User::factory()->create());

        $this->patch(route('admin.messages.archive', $message))->assertRedirect(route('admin.messages.index'));
        $this->assertSame(MessageState::Archived, $message->fresh()->state());

        $this->patch(route('admin.messages.restore', $message))->assertRedirect(route('admin.messages.show', $message));
        $this->assertSame(MessageState::Read, $message->fresh()->state());
    }

    public function test_inbox_hides_archived_messages_by_default(): void
    {
        $visible = ContactMessage::factory()->create();
        $archived = ContactMessage::factory()->archived()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('admin.messages.index'))
            ->assertSee($visible->email)
            ->assertDontSee($archived->email);
    }

    public function test_volunteer_review_records_reviewer(): void
    {
        $reviewer = User::factory()->create();
        $volunteer = Volunteer::factory()->create();

        $this->actingAs($reviewer)
            ->put(route('admin.volunteers.update', $volunteer), ['status' => VolunteerStatus::Accepted->value, 'notes' => 'تم الاتصال وتحديد موعد.'])
            ->assertRedirect(route('admin.volunteers.show', $volunteer));

        $volunteer->refresh();
        $this->assertSame(VolunteerStatus::Accepted, $volunteer->status);
        $this->assertSame($reviewer->id, $volunteer->reviewed_by);
    }
}
