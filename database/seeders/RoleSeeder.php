<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Support\Permissions;
use Database\Seeders\Concerns\SeedsTranslations;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class RoleSeeder extends Seeder
{
    use SeedsTranslations;

    private const array CONTENT = ['projects', 'losses', 'damages', 'stories', 'gallery', 'partners', 'faqs', 'pages', 'donation_methods'];

    private const array INBOX = ['volunteers', 'messages'];

    public function run(Permissions $permissions): void
    {
        $all = $permissions->all();
        $within = fn (array $modules, array $actions): array => array_values(array_filter(
            $all,
            fn (string $permission): bool => in_array(Str::before($permission, '.'), $modules, true) && in_array(Str::after($permission, '.'), $actions, true),
        ));

        $administrator = $this->seedTranslated(Role::class, ['name', ['ar' => 'مدير النظام', 'en' => 'System administrator']], []);
        $administrator->forceFill(['is_system' => true])->save();

        $defaults = [
            [['ar' => 'محرر المحتوى', 'en' => 'Content editor'], [...$within(self::CONTENT, ['view', 'create', 'update', 'delete']), ...$within(self::INBOX, ['view', 'update'])]],
            [['ar' => 'مدخل بيانات', 'en' => 'Data entry'], [...$within(self::CONTENT, ['view', 'create', 'update']), ...$within(self::INBOX, ['view'])]],
            [['ar' => 'مشاهد', 'en' => 'Viewer'], $within([...self::CONTENT, ...self::INBOX], ['view'])],
        ];

        foreach ($defaults as [$name, $granted]) {
            $role = $this->seedTranslated(Role::class, ['name', $name], []);

            if ($role->wasRecentlyCreated) {
                $role->syncPermissions($granted);
            }
        }
    }
}
