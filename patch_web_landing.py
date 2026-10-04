import re

with open('/Users/user/Work/masjid-system/routes/web.php', 'r') as f:
    content = f.read()

new_route = """// Public routes
Route::get('/tentang-kami', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
})->name('public.landing');
"""

content = content.replace("// Public routes", new_route)

with open('/Users/user/Work/masjid-system/routes/web.php', 'w') as f:
    f.write(content)
