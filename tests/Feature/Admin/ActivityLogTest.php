<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->superAdmin()->create(['name' => 'المالك']);
        $this->actingAs($this->owner)->withSession(['locale' => 'ar']);
    }

    public function test_creating_editing_and_deleting_are_recorded_with_the_user_and_label(): void
    {
        $project = Project::factory()->create(['title' => ['ar' => 'مشروع الاختبار']]);
        $project->update(['is_published' => false, 'raised_amount' => 10]);
        $project->delete();

        $logs = ActivityLog::query()->orderBy('id')->get();

        $this->assertSame(['created', 'updated', 'deleted'], $logs->pluck('event')->all());
        $this->assertSame([$this->owner->id], $logs->pluck('user_id')->unique()->all());
        $this->assertSame(['Project'], $logs->pluck('subject_type')->unique()->all());
        $this->assertSame('مشروع الاختبار', $logs->first()->subject_label);
        $this->assertEqualsCanonicalizing(['is_published', 'raised_amount'], $logs[1]->changes);
    }

    public function test_actions_without_a_signed_in_user_are_not_recorded(): void
    {
        auth()->logout();

        Project::factory()->create();

        $this->assertSame(0, ActivityLog::query()->count());
    }

    public function test_noise_and_secrets_are_never_stored(): void
    {
        $this->owner->forceFill(['last_login_at' => now()])->save();
        $this->assertSame(0, ActivityLog::query()->where('subject_type', 'User')->count());

        $this->owner->update(['password' => 'Another-Pass-1', 'name' => 'اسم جديد']);

        $log = ActivityLog::query()->where('subject_type', 'User')->sole();
        $this->assertSame(['name'], $log->changes);
    }

    public function test_the_impersonator_is_recorded_instead_of_the_impersonated_user(): void
    {
        $member = User::factory()->administrator()->create();
        $this->actingAs($member)->withSession(['impersonator_id' => $this->owner->id]);

        Project::factory()->create();

        $this->assertSame($this->owner->id, ActivityLog::query()->where('subject_type', 'Project')->sole()->user_id);
    }

    public function test_only_the_super_admin_can_open_the_activity_log(): void
    {
        ActivityLog::factory()->create(['subject_label' => 'سجل ظاهر']);

        $this->get(route('admin.activity.index'))
            ->assertOk()
            ->assertSee('سجل ظاهر');

        $this->actingAs(User::factory()->administrator()->create())
            ->get(route('admin.activity.index'))
            ->assertForbidden();

        $this->actingAs(User::factory()->viewer()->create())
            ->get(route('admin.activity.index'))
            ->assertForbidden();
    }

    public function test_the_log_can_be_filtered(): void
    {
        $other = User::factory()->create(['name' => 'موظف آخر']);
        ActivityLog::factory()->create(['user_id' => $this->owner, 'event' => 'created', 'subject_type' => 'Project', 'subject_label' => 'عنصر أول']);
        ActivityLog::factory()->create(['user_id' => $other, 'event' => 'deleted', 'subject_type' => 'Story', 'subject_label' => 'عنصر ثان']);

        $this->get(route('admin.activity.index', ['user' => $other->id]))
            ->assertSee('عنصر ثان')->assertDontSee('عنصر أول');

        $this->get(route('admin.activity.index', ['subject' => 'Project']))
            ->assertSee('عنصر أول')->assertDontSee('عنصر ثان');

        $this->get(route('admin.activity.index', ['event' => 'deleted']))
            ->assertSee('عنصر ثان')->assertDontSee('عنصر أول');

        $this->get(route('admin.activity.index', ['q' => 'أول']))
            ->assertSee('عنصر أول')->assertDontSee('عنصر ثان');

        $this->get(route('admin.activity.index', ['event' => 'bogus']))
            ->assertOk()->assertSee('عنصر أول')->assertSee('عنصر ثان');
    }

    public function test_bulk_actions_are_recorded_per_item(): void
    {
        $projects = Project::factory()->count(2)->create();
        ActivityLog::query()->delete();

        $this->post(route('admin.projects.bulk'), ['action' => 'delete', 'ids' => $projects->pluck('id')->all()]);

        $this->assertSame(2, ActivityLog::query()->where('event', 'deleted')->count());
    }
}
