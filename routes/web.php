<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/tentang', 'profile')->name('profile');
Route::get('/proyek', [ProjectController::class, 'publicIndex'])->name('projects.index');
Route::get('/proyek/{project}', [ProjectController::class, 'publicShow'])->name('projects.show');
Route::view('/games', 'games')->name('games');
Route::get('/mulai-proyek', [OrderController::class, 'create'])->name('orders.create');
Route::post('/mulai-proyek', [OrderController::class, 'store'])->middleware('throttle:8,1')->name('orders.store');
Route::view('/permintaan-terkirim', 'orders.success')->name('orders.success');
Route::post('/chatbot/message', [ChatbotController::class, 'respond'])->middleware('throttle:15,1')->name('chatbot.respond');
Route::redirect('/profil', '/tentang');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /chatbot/\nSitemap: ".url('/sitemap.xml')."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('seo.robots');

Route::get('/sitemap.xml', function () {
    $urls = collect([
        ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
        ['loc' => route('profile'), 'changefreq' => 'monthly', 'priority' => '0.9'],
        ['loc' => route('projects.index'), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => route('games'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => route('orders.create'), 'changefreq' => 'monthly', 'priority' => '0.8'],
    ]);

    $urls = $urls->merge(App\Models\Project::query()->where('is_published', true)->get()->map(fn ($project) => [
        'loc' => route('projects.show', $project), 'changefreq' => 'monthly', 'priority' => '0.7',
    ]));

    $xml = $urls->map(fn ($item) => '<url><loc>'.e($item['loc']).'</loc><changefreq>'.$item['changefreq'].'</changefreq><priority>'.$item['priority'].'</priority></url>')->implode('');

    return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$xml.'</urlset>', 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('seo.sitemap');

Route::post('/admin/login', function (Request $request) {
    $credentials = $request->validate(['username' => ['required', 'string'], 'password' => ['required', 'string']]);
    $valid = config('services.admin.username') && config('services.admin.password')
        && hash_equals((string) config('services.admin.username'), $credentials['username'])
        && hash_equals((string) config('services.admin.password'), $credentials['password']);

    if (! $valid) {
        return back()->withErrors(['login' => 'Username atau password tidak sesuai.'])->with('open_admin_login', true);
    }
    $request->session()->regenerate();
    $request->session()->put('is_admin', true);

    return redirect()->route('admin.index');
})->middleware('throttle:5,1')->name('admin.login');

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

Route::prefix('admin')->middleware('admin.session')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::resource('projects', ProjectController::class)->except('show');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
});
