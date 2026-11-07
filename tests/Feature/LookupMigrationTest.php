<?php

namespace Tests\Feature;

use App\Models\Damage;
use App\Models\Project;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class LookupMigrationTest extends TestCase
{
    use RefreshDatabase;

    private const string PATH = 'database/migrations/2026_11_06_223019_create_lookups_table.php';

    public function test_existing_values_move_to_lookups_and_back(): void
    {
        $this->artisan('migrate:rollback', ['--path' => self::PATH])->assertSuccessful();

        $timestamps = ['created_at' => now(), 'updated_at' => now()];
        $project = DB::table('projects')->insertGetId([
            'title' => '{"ar":"مشروع"}', 'summary' => '{"ar":"ملخص"}', 'body' => '{"ar":"نص"}', 'status' => 3, ...$timestamps,
        ]);
        $damage = DB::table('damages')->insertGetId([
            'governorate' => 'rafah', 'category' => 'vehicles', 'item' => '{"ar":"رافعة"}', 'amount' => 500, ...$timestamps,
        ]);
        $volunteer = DB::table('volunteers')->insertGetId([
            'name' => 'سارة', 'email' => 'sara@example.com', 'phone' => '0599', 'country' => 'فلسطين',
            'expertise' => 4, 'experience_years' => 3, 'message' => 'رسالة', 'status' => 1, ...$timestamps,
        ]);

        $this->artisan('migrate', ['--path' => self::PATH])->assertSuccessful();

        $this->assertSame('مكتمل', Project::query()->with('status')->findOrFail($project)->status->name);
        $this->assertSame('سيارات وآليات ومولدات', Damage::query()->with('category')->findOrFail($damage)->category->name);
        $this->assertSame('حلول الطاقة المتجددة والهجينة', Volunteer::query()->with('expertise')->findOrFail($volunteer)->expertise->name);

        $this->artisan('migrate:rollback', ['--path' => self::PATH])->assertSuccessful();

        $this->assertSame(3, (int) DB::table('projects')->where('id', $project)->value('status'));
        $this->assertSame('vehicles', DB::table('damages')->where('id', $damage)->value('category'));
        $this->assertSame(4, (int) DB::table('volunteers')->where('id', $volunteer)->value('expertise'));

        $this->artisan('migrate', ['--path' => self::PATH])->assertSuccessful();
    }
}
