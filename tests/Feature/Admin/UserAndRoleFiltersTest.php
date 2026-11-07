<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UserAndRoleFiltersTest extends TestCase
{
    use RefreshDatabase;

    private Role $editors;

    private Role $viewers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->editors = Role::factory()->editor()->create(['name' => ['ar' => 'محررون']]);
        $this->viewers = Role::factory()->viewer()->create(['name' => ['ar' => 'مشاهدون']]);

        User::factory()->create(['name' => 'أحمد المحرر', 'role_id' => $this->editors]);
        User::factory()->create(['name' => 'سارة المشاهدة', 'role_id' => $this->viewers]);
        User::factory()->inactive()->create(['name' => 'خالد الموقوف', 'role_id' => $this->viewers]);

        $this->actingAs(User::factory()->superAdmin()->create(['name' => 'المالك']))->withSession(['locale' => 'ar']);
    }

    public function test_users_are_filtered_by_role(): void
    {
        $this->get(route('admin.users.index', ['role' => $this->viewers->id]))
            ->assertOk()
            ->assertSee('سارة المشاهدة')
            ->assertSee('خالد الموقوف')
            ->assertDontSee('أحمد المحرر');
    }

    public function test_users_are_filtered_by_status(): void
    {
        $this->get(route('admin.users.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('خالد الموقوف')
            ->assertDontSee('أحمد المحرر');

        $this->get(route('admin.users.index', ['status' => 'active']))
            ->assertOk()
            ->assertSee('أحمد المحرر')
            ->assertDontSee('خالد الموقوف');
    }

    public function test_user_filters_combine_with_search_and_ignore_invalid_values(): void
    {
        $this->get(route('admin.users.index', ['role' => $this->viewers->id, 'status' => 'active', 'q' => 'سارة']))
            ->assertOk()
            ->assertSee('سارة المشاهدة')
            ->assertDontSee('خالد الموقوف');

        $this->get(route('admin.users.index', ['status' => 'bogus', 'role' => 'x']))->assertOk()->assertSee('أحمد المحرر')->assertSee('خالد الموقوف');
    }

    public function test_user_filter_form_lists_roles_and_statuses(): void
    {
        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('كل الأدوار')
            ->assertSee('محررون')
            ->assertSee('data-clean-url', false);
    }

    public function test_roles_are_filtered_by_name_type_and_usage(): void
    {
        $unused = Role::factory()->create(['name' => ['ar' => 'دور بلا مستخدمين']]);

        $this->get(route('admin.roles.index', ['q' => 'مشاهدون']))->assertOk()->assertSee('مشاهدون')->assertDontSee('محررون');

        $this->get(route('admin.roles.index', ['type' => 'system']))->assertOk()->assertSee('دور النظام')->assertDontSee('محررون');
        $this->get(route('admin.roles.index', ['type' => 'custom']))->assertOk()->assertSee('محررون')->assertSee('مشاهدون');

        $this->get(route('admin.roles.index', ['usage' => 'unused']))->assertOk()->assertSee($unused->name)->assertDontSee('محررون');
        $this->get(route('admin.roles.index', ['usage' => 'used']))->assertOk()->assertSee('محررون')->assertDontSee($unused->name);
    }

    public function test_roles_empty_state_and_invalid_filters(): void
    {
        $this->get(route('admin.roles.index', ['q' => 'لا يوجد دور بهذا الاسم']))->assertOk()->assertSee('لا توجد أدوار مطابقة.');

        $this->get(route('admin.roles.index', ['type' => 'bogus', 'usage' => 'bogus']))->assertOk()->assertSee('محررون');
    }
}
