<?php

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;
use App\Models\Damage;
use App\Models\DonationMethod;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Loss;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Story;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_every_screen(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->urgent()->create();
        $story = Story::factory()->for($project)->create();
        $loss = Loss::factory()->create();
        $partner = Partner::factory()->create();
        $faq = Faq::factory()->create();
        $item = GalleryItem::factory()->create();
        $damage = Damage::factory()->create();
        $link = DonationMethod::factory()->create();
        $bank = DonationMethod::factory()->bank()->create();
        $volunteer = Volunteer::factory()->create();
        $message = ContactMessage::factory()->create();
        $editor = User::factory()->create();

        $urls = [
            route('admin.dashboard'),
            route('admin.projects.index'), route('admin.projects.create'), route('admin.projects.edit', $project),
            route('admin.losses.index'), route('admin.losses.create'), route('admin.losses.edit', $loss),
            route('admin.damages.index'), route('admin.damages.create'), route('admin.damages.edit', $damage),
            route('admin.stories.index'), route('admin.stories.create'), route('admin.stories.edit', $story),
            route('admin.gallery.index'), route('admin.gallery.create'), route('admin.gallery.edit', $item),
            route('admin.partners.index'), route('admin.partners.create'), route('admin.partners.edit', $partner),
            route('admin.faqs.index'), route('admin.faqs.create'), route('admin.faqs.edit', $faq),
            route('admin.donation-methods.index'), route('admin.donation-methods.create'),
            route('admin.donation-methods.edit', $link), route('admin.donation-methods.edit', $bank),
            route('admin.volunteers.index'), route('admin.volunteers.show', $volunteer),
            route('admin.messages.index'), route('admin.messages.show', $message),
            route('admin.users.index'), route('admin.users.create'), route('admin.users.edit', $editor), route('admin.users.edit', $admin),
            route('admin.users.permissions', $editor),
            route('admin.roles.index'), route('admin.roles.create'), route('admin.roles.edit', $editor->role),
            route('admin.settings.edit'),
            route('admin.profile.edit'),
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk()->assertHeader('Cache-Control', 'max-age=0, no-store, private');
        }
    }

    public function test_index_filters_render(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('admin.projects.index', ['q' => 'طاقة', 'status' => 2, 'visibility' => 'hidden']))->assertOk();
        $this->get(route('admin.volunteers.index', ['q' => 'a', 'status' => 1, 'expertise' => 4]))->assertOk();
        $this->get(route('admin.messages.index', ['state' => 'archived']))->assertOk();
    }
}
