<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Support\Localization;
use App\Support\Permissions;
use App\Support\SiteSettings;
use App\View\Composers\InboxComposer;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(SiteSettings::class);
        $this->app->scoped(Localization::class);
        $this->app->singleton(Permissions::class);
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        CarbonImmutable::setLocale(config('app.locale'));
        URL::defaults(['locale' => config('app.locale')]);

        Model::shouldBeStrict(! $this->app->isProduction());

        Blade::directive('confirm', fn (string $expression): string => "<?php echo \\App\\Support\\Confirm::attributes({$expression}); ?>");

        Gate::before(fn (User $user, string $ability): ?bool => $this->app->make(Permissions::class)->exists($ability) ? $user->hasPermission($ability) : null);

        View::composer('admin.partials.header', InboxComposer::class);

        RateLimiter::for('forms', fn (Request $request): Limit => Limit::perMinute(5)->by('forms|'.$request->ip()));

        Password::defaults(fn () => Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->when($this->app->isProduction(), fn (Password $rule) => $rule->uncompromised(3)));
    }
}
