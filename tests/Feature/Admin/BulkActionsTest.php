<?php

namespace Tests\Feature\Admin;

use App\Models\Damage;
use App\Models\GalleryItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BulkActionsTest extends TestCase
{
    use RefreshDatabase;

    private function signIn(?User $user = null): User
    {
        $user ??= User::factory()->administrator()->create();
        $this->actingAs($user)->withSession(['locale' => 'ar']);

        return $user;
    }

    public function test_selected_items_can_be_hidden_and_published_together(): void
    {
        $this->signIn();
        $selected = Project::factory()->count(2)->create();
        $untouched = Project::factory()->create();

        $this->post(route('admin.projects.bulk'), ['action' => 'hide', 'ids' => $selected->pluck('id')->all()])
            ->assertRedirect(route('admin.projects.index'))
            ->assertSessionHas('success');

        $this->assertSame(0, Project::query()->whereKey($selected->pluck('id'))->where('is_published', true)->count());
        $this->assertTrue($untouched->fresh()->is_published);

        $this->post(route('admin.projects.bulk'), ['action' => 'publish', 'ids' => $selected->pluck('id')->all()])
            ->assertRedirect();

        $this->assertSame(2, Project::query()->whereKey($selected->pluck('id'))->where('is_published', true)->count());
    }

    public function test_selected_items_can_be_deleted_together(): void
    {
        $this->signIn();
        $selected = Project::factory()->count(2)->create();
        $kept = Project::factory()->create();

        $this->post(route('admin.projects.bulk'), ['action' => 'delete', 'ids' => $selected->pluck('id')->all()])
            ->assertRedirect(route('admin.projects.index'));

        $this->assertSame(1, Project::query()->count());
        $this->assertTrue(Project::query()->whereKey($kept->id)->exists());
    }

    public function test_modules_without_visibility_only_accept_delete(): void
    {
        $this->signIn();
        $damages = Damage::factory()->count(2)->create();

        $this->post(route('admin.damages.bulk'), ['action' => 'hide', 'ids' => $damages->pluck('id')->all()])
            ->assertSessionHasErrors('action');

        $this->post(route('admin.damages.bulk'), ['action' => 'delete', 'ids' => $damages->pluck('id')->all()])
            ->assertRedirect(route('admin.damages.index'));

        $this->assertSame(0, Damage::query()->count());
    }

    public function test_the_selection_is_validated(): void
    {
        $this->signIn();

        $this->post(route('admin.projects.bulk'), ['action' => 'delete', 'ids' => []])->assertSessionHasErrors('ids');
        $this->post(route('admin.projects.bulk'), ['action' => 'explode', 'ids' => [1]])->assertSessionHasErrors('action');
        $this->post(route('admin.projects.bulk'), ['action' => 'delete', 'ids' => ['abc']])->assertSessionHasErrors('ids.0');
    }

    public function test_deleting_needs_the_delete_permission_even_with_update_permission(): void
    {
        $this->signIn(User::factory()->withPermissions(['projects.view', 'projects.update'])->create());
        $project = Project::factory()->create();

        $this->post(route('admin.projects.bulk'), ['action' => 'delete', 'ids' => [$project->id]])->assertForbidden();
        $this->assertTrue(Project::query()->whereKey($project->id)->exists());

        $this->post(route('admin.projects.bulk'), ['action' => 'hide', 'ids' => [$project->id]])->assertRedirect();
        $this->assertFalse($project->fresh()->is_published);
    }

    public function test_viewers_cannot_use_bulk_actions_and_do_not_see_the_checkboxes(): void
    {
        $this->signIn(User::factory()->viewer()->create());
        $project = Project::factory()->create();

        $this->post(route('admin.projects.bulk'), ['action' => 'hide', 'ids' => [$project->id]])->assertForbidden();

        $this->get(route('admin.projects.index'))
            ->assertOk()
            ->assertDontSee('data-row-check', false);
    }

    public function test_list_screens_render_the_selection_controls(): void
    {
        $this->signIn();
        Project::factory()->create();
        GalleryItem::factory()->create();

        $this->get(route('admin.projects.index'))
            ->assertOk()
            ->assertSee('data-row-check', false)
            ->assertSee('data-all-check', false);

        $this->get(route('admin.gallery.index'))
            ->assertOk()
            ->assertSee('data-row-check', false);
    }
}
