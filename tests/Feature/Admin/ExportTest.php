<?php

namespace Tests\Feature\Admin;

use App\Enums\LookupType;
use App\Enums\VolunteerStatus;
use App\Models\ContactMessage;
use App\Models\Damage;
use App\Models\Lookup;
use App\Models\Loss;
use App\Models\Project;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;

final class ExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create())->withSession(['locale' => 'ar']);
    }

    /**
     * @return list<list<mixed>>
     */
    private function rows(TestResponse $response): array
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx').'.xlsx';
        file_put_contents($path, $response->streamedContent());

        $reader = new Reader;
        $reader->open($path);
        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
        }

        $reader->close();
        unlink($path);

        return $rows;
    }

    public function test_projects_are_exported_with_the_current_filters(): void
    {
        $planned = Lookup::query()->ofType(LookupType::ProjectStatus)->ordered()->firstOrFail();
        Project::factory()->create(['title' => ['ar' => 'مشروع مخطط'], 'status_id' => $planned, 'goal_amount' => 1000, 'raised_amount' => 250]);
        $completed = Lookup::query()->ofType(LookupType::ProjectStatus)->ordered()->get()->last();
        Project::factory()->create(['title' => ['ar' => 'مشروع آخر'], 'status_id' => $completed, 'is_published' => false]);

        $response = $this->get(route('admin.projects.export', ['status' => $planned->id]));

        $response->assertOk()->assertDownload('projects-'.now()->format('Y-m-d').'.xlsx');
        $this->assertSame('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));

        $rows = $this->rows($response);
        $this->assertCount(2, $rows);
        $this->assertSame('العنوان', $rows[0][1]);
        $this->assertSame('مشروع مخطط', $rows[1][1]);
        $this->assertSame($planned->name, $rows[1][3]);
        $this->assertEquals(1000, $rows[1][4]);
        $this->assertEquals(25, $rows[1][6]);

        $this->assertCount(3, $this->rows($this->get(route('admin.projects.export'))));
        $this->assertCount(2, $this->rows($this->get(route('admin.projects.export', ['visibility' => 'hidden']))));
    }

    public function test_damages_and_losses_are_exported(): void
    {
        $category = Lookup::query()->ofType(LookupType::DamageCategory)->ordered()->firstOrFail();
        Damage::factory()->create(['category_id' => $category, 'item' => ['ar' => 'محولات'], 'amount' => 21_650_000]);
        Loss::factory()->create(['title' => ['ar' => 'خطوط مدمرة'], 'figure' => 70]);

        $damages = $this->rows($this->get(route('admin.damages.export', ['category' => $category->id])));
        $this->assertSame($category->name, $damages[1][2]);
        $this->assertSame('محولات', $damages[1][3]);
        $this->assertEquals(21_650_000, $damages[1][4]);

        $empty = Lookup::factory()->type(LookupType::DamageCategory)->create();
        $this->assertCount(1, $this->rows($this->get(route('admin.damages.export', ['category' => $empty->id]))));

        $losses = $this->rows($this->get(route('admin.losses.export')));
        $this->assertSame('خطوط مدمرة', $losses[1][1]);
        $this->assertEquals(70, $losses[1][2]);
    }

    public function test_inbox_screens_are_exported_and_formulas_stay_text(): void
    {
        Volunteer::factory()->create(['name' => '=HYPERLINK("http://evil.test")', 'status' => VolunteerStatus::Accepted]);
        Volunteer::factory()->create(['status' => VolunteerStatus::Declined]);
        ContactMessage::factory()->create(['name' => 'أحمد']);
        ContactMessage::factory()->archived()->create();

        $volunteers = $this->rows($this->get(route('admin.volunteers.export', ['status' => VolunteerStatus::Accepted->value])));
        $this->assertCount(2, $volunteers);
        $this->assertSame('=HYPERLINK("http://evil.test")', $volunteers[1][1]);

        $this->assertCount(2, $this->rows($this->get(route('admin.messages.export'))));
        $this->assertCount(2, $this->rows($this->get(route('admin.messages.export', ['state' => 'archived']))));
    }

    public function test_export_follows_the_view_permission(): void
    {
        $this->actingAs(User::factory()->withPermissions(['faqs.view'])->create());

        $this->get(route('admin.projects.export'))->assertForbidden();

        $this->actingAs(User::factory()->withPermissions(['projects.view'])->create());

        $this->get(route('admin.projects.export'))->assertOk();
        $this->get(route('admin.messages.export'))->assertForbidden();
    }

    public function test_index_pages_link_to_the_export_with_the_active_filters(): void
    {
        $this->get(route('admin.projects.index', ['q' => 'طاقة']))
            ->assertOk()
            ->assertSee(route('admin.projects.export', ['q' => 'طاقة']), false);

        foreach (['losses', 'damages', 'volunteers', 'messages'] as $screen) {
            $this->get(route("admin.{$screen}.index"))->assertOk()->assertSee(route("admin.{$screen}.export"), false);
        }
    }

    public function test_filter_forms_clear_their_query_from_the_address_bar(): void
    {
        $this->get(route('admin.donation-methods.index', ['visibility' => 'hidden']))
            ->assertOk()
            ->assertSee('data-clean-url', false)
            ->assertSee('autocomplete="off"', false);
    }
}
