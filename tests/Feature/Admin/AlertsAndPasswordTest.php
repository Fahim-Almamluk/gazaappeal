<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

final class AlertsAndPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create())->withSession(['locale' => 'ar']);
    }

    /**
     * @return array{type: string, title: string, details: list<array{field: string, text: string}>}|null
     */
    private function flash(TestResponse $response): ?array
    {
        preg_match('#<script type="application/json" data-flash>(.*?)</script>#s', $response->getContent(), $matches);

        return isset($matches[1]) ? json_decode($matches[1], true, flags: JSON_THROW_ON_ERROR) : null;
    }

    private function userData(string $password, array $overrides = []): array
    {
        return [
            'name' => 'محرر جديد',
            'email' => 'editor@gedco.ps',
            'role_id' => Role::factory()->editor()->create()->id,
            'password' => $password,
            'password_confirmation' => $password,
            ...$overrides,
        ];
    }

    public function test_eight_character_password_with_case_and_digit_is_accepted(): void
    {
        $this->post(route('admin.users.store'), $this->userData('Abcdefg1'))->assertSessionHasNoErrors();

        $this->assertSame('editor@gedco.ps', User::query()->where('email', 'editor@gedco.ps')->value('email'));
    }

    public function test_weak_passwords_are_rejected_with_clear_messages(): void
    {
        $this->post(route('admin.users.store'), $this->userData('Ab1defg'))
            ->assertSessionHasErrors(['password' => 'يجب أن تتكوّن كلمة المرور من 8 خانات على الأقل.']);

        $this->post(route('admin.users.store'), $this->userData('abcdefg1'))
            ->assertSessionHasErrors(['password' => 'يجب أن تحتوي كلمة المرور على حرف كبير وحرف صغير (A و a) على الأقل.']);

        $this->post(route('admin.users.store'), $this->userData('Abcdefgh'))
            ->assertSessionHasErrors(['password' => 'يجب أن تحتوي كلمة المرور على رقم واحد على الأقل.']);

        $this->assertDatabaseMissing('users', ['email' => 'editor@gedco.ps']);
    }

    public function test_password_fields_show_the_rules_and_strength_meter(): void
    {
        foreach ([route('admin.users.create'), route('admin.profile.edit')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('x-data="passwordStrength"', false)
                ->assertSee('8 خانات على الأقل')
                ->assertSee('حرف كبير وحرف صغير (A و a)')
                ->assertSee('data-weak="ضعيفة"', false)
                ->assertDontSee('12 حرفًا');
        }

        $this->get(route('login'))->assertDontSee('passwordStrength', false)->assertDontSee('12 حرفًا');
    }

    public function test_success_is_delivered_as_data_for_the_script_not_as_markup(): void
    {
        $response = $this->followingRedirects()->post(route('admin.users.store'), $this->userData('Abcdefg1'));

        $flash = $this->flash($response);
        $this->assertSame('success', $flash['type']);
        $this->assertSame('تمت إضافة المستخدم.', $flash['title']);
        $response->assertDontSee('role="status"', false)->assertDontSee('x-data="toast"', false);
    }

    public function test_validation_errors_produce_one_short_alert_and_keep_messages_inline(): void
    {
        $response = $this->from(route('admin.users.create'))->followingRedirects()->post(route('admin.users.store'), $this->userData('weak', ['name' => '']));

        $flash = $this->flash($response);
        $this->assertSame('error', $flash['type']);
        $this->assertSame('تعذّر الحفظ، راجع الحقول المعلّمة بالأحمر.', $flash['title']);
        $this->assertSame(['name', 'password'], array_column($flash['details'], 'field'));
        $response->assertSee('aria-invalid="true"', false)->assertSee('يجب أن تتكوّن كلمة المرور من 8 خانات على الأقل.');
    }

    public function test_only_the_first_message_per_field_is_reported(): void
    {
        $flash = $this->flash($this->from(route('admin.users.create'))->followingRedirects()->post(route('admin.users.store'), $this->userData('weak')));

        $this->assertSame(['password'], array_column($flash['details'], 'field'));
    }

    public function test_named_error_bags_are_included(): void
    {
        $flash = $this->flash($this->from(route('admin.profile.edit'))->followingRedirects()->put(route('admin.profile.password'), [
            'current_password' => 'wrong',
            'password' => 'Abcdefg1',
            'password_confirmation' => 'Abcdefg1',
        ]));

        $this->assertSame('error', $flash['type']);
        $this->assertSame(['current_password'], array_column($flash['details'], 'field'));
    }

    public function test_only_one_alert_is_sent_and_the_most_important_wins(): void
    {
        $this->withSession(['error' => 'فشلت العملية', 'warning' => 'تنبيه مهم', 'success' => 'تمت']);

        $flash = $this->flash($this->get(route('admin.dashboard')));

        $this->assertSame(['error', 'فشلت العملية', []], [$flash['type'], $flash['title'], $flash['details']]);
    }

    public function test_admin_site_and_login_pages_share_the_same_alert_source(): void
    {
        $this->withSession(['success' => 'تم الإرسال']);
        $this->assertSame(['success', 'تم الإرسال'], array_values(array_intersect_key($this->flash($this->get('/ar/contact')), array_flip(['type', 'title']))));

        $this->flushSession()->withSession(['locale' => 'ar']);
        $flash = $this->flash($this->followingRedirects()->post(route('logout')));
        $this->assertSame(['success', 'تم تسجيل الخروج بأمان.'], [$flash['type'], $flash['title']]);
    }

    public function test_impersonation_start_has_no_alert_because_the_banner_says_it(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $editor = User::factory()->create();

        $response = $this->actingAs($owner)->followingRedirects()->post(route('admin.users.impersonate', $editor));

        $this->assertNull($this->flash($response));
        $response->assertSee(route('admin.impersonation.leave'), false);
    }

    public function test_flash_data_cannot_break_out_of_the_script_tag(): void
    {
        $this->withSession(['success' => '</script><img src=x onerror=alert(1)>']);

        $html = $this->get(route('admin.dashboard'))->getContent();

        $this->assertStringNotContainsString('</script><img', $html);
        $this->assertSame('</script><img src=x onerror=alert(1)>', json_decode(
            preg_match('#data-flash>(.*?)</script>#s', $html, $m) ? $m[1] : '{}',
            true,
        )['title']);
    }
}
