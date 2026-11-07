<?php

namespace Tests\Feature;

use App\Enums\LookupType;
use App\Enums\VolunteerStatus;
use App\Models\ContactMessage;
use App\Models\Lookup;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PublicFormsTest extends TestCase
{
    use RefreshDatabase;

    private function volunteerData(array $overrides = []): array
    {
        return [
            'name' => 'سارة أحمد',
            'email' => 'sara@example.com',
            'phone' => '+970 59 123 4567',
            'country' => 'فلسطين',
            'expertise_id' => Lookup::query()->ofType(LookupType::VolunteerExpertise)->value('id'),
            'experience_years' => 8,
            'message' => 'مهندسة طاقة شمسية بخبرة في تصميم الأنظمة الهجينة للمرافق الصحية.',
            ...$overrides,
        ];
    }

    private function contactData(array $overrides = []): array
    {
        return [
            'name' => 'محمد علي',
            'email' => 'mohammad@example.com',
            'body' => 'نرغب في الشراكة لتمويل أحد مشاريع الطاقة.',
            ...$overrides,
        ];
    }

    public function test_volunteer_application_is_stored_as_new(): void
    {
        $this->post(route('volunteer.store'), $this->volunteerData())
            ->assertRedirect(route('volunteer.create'))
            ->assertSessionHas('success');

        $volunteer = Volunteer::query()->sole();
        $this->assertSame(VolunteerStatus::New, $volunteer->status);
        $this->assertNull($volunteer->reviewed_by);
    }

    public function test_volunteer_cannot_set_status_or_notes(): void
    {
        $this->post(route('volunteer.store'), $this->volunteerData([
            'status' => VolunteerStatus::Accepted->value,
            'notes' => 'injected',
        ]));

        $volunteer = Volunteer::query()->sole();
        $this->assertSame(VolunteerStatus::New, $volunteer->status);
        $this->assertNull($volunteer->notes);
    }

    public function test_volunteer_application_is_validated(): void
    {
        $this->post(route('volunteer.store'), $this->volunteerData(['email' => 'not-an-email', 'expertise_id' => 99]))
            ->assertSessionHasErrors(['email', 'expertise_id']);

        $this->assertDatabaseCount('volunteers', 0);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post(route('volunteer.store'), $this->volunteerData(['website' => 'spam']))->assertSessionHasErrors('website');
        $this->post(route('contact.store'), $this->contactData(['website' => 'spam']))->assertSessionHasErrors('website');

        $this->assertDatabaseCount('volunteers', 0);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_message_is_stored_unread(): void
    {
        $this->post(route('contact.store'), $this->contactData())
            ->assertRedirect(route('contact.create'))
            ->assertSessionHas('success');

        $message = ContactMessage::query()->sole();
        $this->assertNull($message->read_at);
    }

    public function test_forms_are_rate_limited(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->post(route('contact.store'), $this->contactData())->assertRedirect();
        }

        $this->post(route('contact.store'), $this->contactData())->assertTooManyRequests();
    }
}
