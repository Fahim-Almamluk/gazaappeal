<?php

namespace Tests\Feature\Admin;

use App\Enums\DamageCategory;
use App\Enums\DonationMethodType;
use App\Enums\Governorate;
use App\Enums\LossIcon;
use App\Enums\ProjectStatus;
use App\Models\Damage;
use App\Models\DonationMethod;
use App\Models\GalleryItem;
use App\Models\Loss;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->actingAs(User::factory()->create());
    }

    private function projectData(array $overrides = []): array
    {
        return [
            'title' => ['ar' => 'الطاقة الشمسية لآبار المياه', 'en' => 'Solar power for water wells'],
            'summary' => ['ar' => 'إيصال المياه للمنازل والخيام.', 'en' => ''],
            'body' => ['ar' => "الفقرة الأولى.\n\nالفقرة الثانية.", 'en' => ''],
            'goal_amount' => '50000',
            'raised_amount' => '12500.50',
            'status' => ProjectStatus::Active->value,
            'is_urgent' => '1',
            'is_published' => '1',
            'sort_order' => '1',
            ...$overrides,
        ];
    }

    public function test_editor_creates_project_with_image(): void
    {
        $this->post(route('admin.projects.store'), $this->projectData(['image' => UploadedFile::fake()->image('well.jpg', 1200, 750)]))
            ->assertRedirect(route('admin.projects.index'));

        $project = Project::query()->sole();
        $this->assertTrue($project->is_urgent);
        $this->assertSame(['ar' => 'الطاقة الشمسية لآبار المياه', 'en' => 'Solar power for water wells'], $project->translations('title'));
        $this->assertSame(['ar' => 'إيصال المياه للمنازل والخيام.'], $project->translations('summary'));
        $this->assertSame(25, $project->progress());
        Storage::disk('public')->assertExists($project->image);
    }

    public function test_unchecked_toggles_are_saved_as_false(): void
    {
        $project = Project::factory()->urgent()->create();

        $this->put(route('admin.projects.update', $project), $this->projectData(['is_urgent' => '0', 'is_published' => '0']))
            ->assertRedirect(route('admin.projects.index'));

        $project->refresh();
        $this->assertFalse($project->is_urgent);
        $this->assertFalse($project->is_published);
    }

    public function test_replacing_an_image_removes_the_old_file(): void
    {
        $old = UploadedFile::fake()->image('old.png')->store('projects', 'public');
        $project = Project::factory()->create(['image' => $old]);

        $this->put(route('admin.projects.update', $project), $this->projectData(['image' => UploadedFile::fake()->image('new.webp')]));

        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($project->fresh()->image);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $this->post(route('admin.projects.store'), $this->projectData(['image' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')]))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_svg_upload_is_rejected(): void
    {
        $this->post(route('admin.projects.store'), $this->projectData(['image' => UploadedFile::fake()->create('logo.svg', 2, 'image/svg+xml')]))
            ->assertSessionHasErrors('image');
    }

    public function test_deleting_a_project_soft_deletes_it(): void
    {
        $project = Project::factory()->create();

        $this->delete(route('admin.projects.destroy', $project))->assertRedirect(route('admin.projects.index'));

        $this->assertSoftDeleted($project);
    }

    public function test_loss_is_created(): void
    {
        $this->post(route('admin.losses.store'), [
            'title' => ['ar' => 'شبكات الضغط المتوسط'],
            'figure' => '1200',
            'unit' => ['ar' => 'كم', 'en' => 'km'],
            'description' => ['ar' => 'أطوال الشبكات المدمرة.'],
            'icon' => LossIcon::Cables->value,
            'is_published' => '1',
            'sort_order' => '0',
        ])->assertRedirect(route('admin.losses.index'));

        $this->assertSame(LossIcon::Cables, Loss::query()->sole()->icon);
    }

    public function test_damage_is_created(): void
    {
        $this->post(route('admin.damages.store'), [
            'governorate' => Governorate::KhanYounis->value,
            'category' => DamageCategory::Networks->value,
            'item' => ['ar' => 'محولات التوزيع', 'en' => 'Distribution transformers'],
            'amount' => '21650000',
            'sort_order' => '2',
        ])->assertRedirect(route('admin.damages.index'));

        $damage = Damage::query()->sole();
        $this->assertSame(Governorate::KhanYounis, $damage->governorate);
        $this->assertSame(21_650_000, $damage->amount);
    }

    public function test_damage_rejects_unknown_governorate(): void
    {
        $this->post(route('admin.damages.store'), [
            'governorate' => 'jerusalem',
            'category' => DamageCategory::Networks->value,
            'item' => ['ar' => 'محولات التوزيع'],
            'amount' => '1000',
            'sort_order' => '0',
        ])->assertSessionHasErrors('governorate');
    }

    public function test_gallery_item_requires_an_image(): void
    {
        $this->post(route('admin.gallery.store'), ['caption' => ['ar' => 'من الميدان'], 'sort_order' => '0'])
            ->assertSessionHasErrors('image');

        $this->assertSame(0, GalleryItem::query()->count());
    }

    public function test_bank_method_requires_account_details_and_drops_url(): void
    {
        $this->post(route('admin.donation-methods.store'), [
            'type' => DonationMethodType::BankTransfer->value,
            'name' => ['ar' => 'تحويل بنكي'],
            'sort_order' => '0',
        ])->assertSessionHasErrors(['bank_details.bank_name', 'bank_details.beneficiary', 'bank_details.account_number']);

        $this->post(route('admin.donation-methods.store'), [
            'type' => DonationMethodType::BankTransfer->value,
            'name' => ['ar' => 'تحويل بنكي', 'en' => 'Bank transfer'],
            'url' => 'https://example.com',
            'bank_details' => ['bank_name' => 'بنك فلسطين', 'bank_name_en' => 'Bank of Palestine', 'beneficiary' => 'شركة الكهرباء', 'account_number' => '27428', 'iban' => 'PS05PALS045400274280013000000'],
            'is_published' => '1',
            'sort_order' => '0',
        ])->assertRedirect(route('admin.donation-methods.index'));

        $method = DonationMethod::query()->sole();
        $this->assertNull($method->url);
        $this->assertSame('27428', $method->bankDetail('account_number'));
        $this->assertSame('Bank of Palestine', $method->bankDetail('bank_name', 'en'));
        $this->assertSame('بنك فلسطين', $method->bankDetail('bank_name', 'ar'));
    }

    public function test_link_method_requires_https_url(): void
    {
        $this->post(route('admin.donation-methods.store'), [
            'type' => DonationMethodType::Link->value,
            'name' => ['ar' => 'منصة'],
            'url' => 'javascript:alert(1)',
            'sort_order' => '0',
        ])->assertSessionHasErrors('url');
    }

    public function test_arabic_text_is_required_and_english_is_optional(): void
    {
        $this->post(route('admin.projects.store'), $this->projectData(['title' => ['ar' => '', 'en' => 'Only English']]))
            ->assertSessionHasErrors('title.ar');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_unknown_languages_are_rejected(): void
    {
        $this->post(route('admin.projects.store'), $this->projectData(['title' => ['ar' => 'عنوان', 'fr' => 'Titre']]))
            ->assertSessionHasErrors('title');
    }

    public function test_clearing_the_english_text_falls_back_to_arabic(): void
    {
        $project = Project::factory()->create(['title' => ['ar' => 'عنوان', 'en' => 'Title']]);

        $this->put(route('admin.projects.update', $project), $this->projectData(['title' => ['ar' => 'عنوان', 'en' => '']]));

        $project->refresh();
        $this->assertSame(['ar' => 'عنوان'], $project->translations('title'));
        $this->assertSame('عنوان', $project->translation('title', 'en'));
    }
}
