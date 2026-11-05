<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const int LEGACY_ADMIN = 1;

    private const array EDITOR_PERMISSIONS = [
        'projects' => ['view', 'create', 'update', 'delete'],
        'losses' => ['view', 'create', 'update', 'delete'],
        'damages' => ['view', 'create', 'update', 'delete'],
        'stories' => ['view', 'create', 'update', 'delete'],
        'gallery' => ['view', 'create', 'update', 'delete'],
        'partners' => ['view', 'create', 'update', 'delete'],
        'faqs' => ['view', 'create', 'update', 'delete'],
        'donation_methods' => ['view', 'create', 'update', 'delete'],
        'volunteers' => ['view', 'update'],
        'messages' => ['view', 'update'],
    ];

    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('permission', 60);

            $table->primary(['role_id', 'permission']);
        });

        Schema::create('permission_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('permission', 60);
            $table->boolean('granted');

            $table->primary(['user_id', 'permission']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained()->restrictOnDelete();
        });

        $now = now();
        $adminRole = DB::table('roles')->insertGetId([
            'name' => json_encode(['ar' => 'مدير النظام', 'en' => 'System administrator'], JSON_UNESCAPED_UNICODE),
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $editorRole = DB::table('roles')->insertGetId([
            'name' => json_encode(['ar' => 'محرر المحتوى', 'en' => 'Content editor'], JSON_UNESCAPED_UNICODE),
            'is_system' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('permission_role')->insert(
            collect(self::EDITOR_PERMISSIONS)
                ->flatMap(fn (array $actions, string $module): array => array_map(fn (string $action): array => ['role_id' => $editorRole, 'permission' => "{$module}.{$action}"], $actions))
                ->values()
                ->all(),
        );

        DB::table('users')->where('role', self::LEGACY_ADMIN)->update(['role_id' => $adminRole]);
        DB::table('users')->where('role', '!=', self::LEGACY_ADMIN)->update(['role_id' => $editorRole]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('role')->default(2)->after('password');
        });

        $systemRoles = DB::table('roles')->where('is_system', true)->pluck('id');
        DB::table('users')->whereIn('role_id', $systemRoles)->update(['role' => self::LEGACY_ADMIN]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('roles');
    }
};
