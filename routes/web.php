<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Site;
use Illuminate\Support\Facades\Route;

$locales = array_keys(config('appeal.locales'));

Route::get('/', Site\LocaleRedirectController::class)->name('root');
Route::get('/robots.txt', Site\RobotsController::class)->name('robots');
Route::get('/sitemap.xml', Site\SitemapController::class)->name('sitemap');
Route::get('/locale/{locale}', Site\SwitchLocaleController::class)->whereIn('locale', $locales)->name('locale.switch');

Route::prefix('{locale}')->whereIn('locale', $locales)->group(function () {
    Route::get('/', Site\HomeController::class)->name('home');
    Route::get('/about', Site\AboutController::class)->name('about');
    Route::get('/losses', Site\LossController::class)->name('losses');
    Route::get('/projects', [Site\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [Site\ProjectController::class, 'show'])->whereNumber('project')->name('projects.show');
    Route::get('/stories', [Site\StoryController::class, 'index'])->name('stories.index');
    Route::get('/stories/{story}', [Site\StoryController::class, 'show'])->whereNumber('story')->name('stories.show');
    Route::get('/gallery', Site\GalleryController::class)->name('gallery');
    Route::get('/donate', Site\DonateController::class)->name('donate');
    Route::get('/faq', Site\FaqController::class)->name('faq');
    Route::view('/privacy', 'site.privacy')->name('privacy');

    Route::get('/volunteer', [Site\VolunteerController::class, 'create'])->name('volunteer.create');
    Route::post('/volunteer', [Site\VolunteerController::class, 'store'])->middleware('throttle:forms')->name('volunteer.store');
    Route::get('/contact', [Site\ContactController::class, 'create'])->name('contact.create');
    Route::post('/contact', [Site\ContactController::class, 'store'])->middleware('throttle:forms')->name('contact.store');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'active', 'no-store', 'permission'])
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('projects/export', [Admin\ProjectController::class, 'export'])->name('projects.export');
        Route::get('losses/export', [Admin\LossController::class, 'export'])->name('losses.export');
        Route::get('damages/export', [Admin\DamageController::class, 'export'])->name('damages.export');
        Route::get('volunteers/export', [Admin\VolunteerController::class, 'export'])->name('volunteers.export');
        Route::get('messages/export', [Admin\MessageController::class, 'export'])->name('messages.export');

        Route::resource('projects', Admin\ProjectController::class)->except('show');
        Route::resource('losses', Admin\LossController::class)->except('show');
        Route::resource('damages', Admin\DamageController::class)->except('show');
        Route::resource('stories', Admin\StoryController::class)->except('show');
        Route::resource('gallery', Admin\GalleryItemController::class)->except('show')->parameters(['gallery' => 'galleryItem']);
        Route::resource('partners', Admin\PartnerController::class)->except('show');
        Route::resource('faqs', Admin\FaqController::class)->except('show');
        Route::resource('donation-methods', Admin\DonationMethodController::class)->except('show')->parameters(['donation-methods' => 'donationMethod']);

        Route::get('volunteers', [Admin\VolunteerController::class, 'index'])->name('volunteers.index');
        Route::get('volunteers/{volunteer}', [Admin\VolunteerController::class, 'show'])->name('volunteers.show');
        Route::put('volunteers/{volunteer}', [Admin\VolunteerController::class, 'update'])->name('volunteers.update');

        Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::patch('messages/{message}/archive', [Admin\MessageController::class, 'archive'])->name('messages.archive');
        Route::patch('messages/{message}/restore', [Admin\MessageController::class, 'restore'])->name('messages.restore');

        Route::resource('users', Admin\UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
        Route::get('users/{user}/permissions', [Admin\UserPermissionController::class, 'edit'])->name('users.permissions');
        Route::put('users/{user}/permissions', [Admin\UserPermissionController::class, 'update'])->name('users.permissions.update');

        Route::post('users/{user}/impersonate', [Admin\ImpersonationController::class, 'store'])->name('users.impersonate');
        Route::delete('impersonation', [Admin\ImpersonationController::class, 'destroy'])->name('impersonation.leave');

        Route::resource('roles', Admin\RoleController::class)->except('show');
        Route::resource('lookups', Admin\LookupController::class)->except('show');

        Route::put('menu/order', [Admin\MenuController::class, 'reorder'])->name('menu.reorder');
        Route::post('menu/reset', [Admin\MenuController::class, 'reset'])->name('menu.reset');
        Route::resource('menu', Admin\MenuController::class)->except('show')->parameters(['menu' => 'menuItem']);

        Route::get('home-sections', [Admin\HomeSectionController::class, 'edit'])->name('home-sections.edit');
        Route::put('home-sections', [Admin\HomeSectionController::class, 'update'])->name('home-sections.update');
        Route::post('home-sections/reset', [Admin\HomeSectionController::class, 'reset'])->name('home-sections.reset');

        Route::get('pages', [Admin\PageContentController::class, 'edit'])->name('pages.edit');
        Route::put('pages', [Admin\PageContentController::class, 'update'])->name('pages.update');

        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [Admin\ProfileController::class, 'password'])->middleware('throttle:6,1')->name('profile.password');
    });

Route::get('{path}', Site\LocaleRedirectController::class)
    ->where('path', '(about|losses|projects|stories|gallery|donate|faq|privacy|volunteer|contact)(/.*)?');
