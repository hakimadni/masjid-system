import re

with open('/Users/user/Work/masjid-system/routes/web.php', 'r') as f:
    content = f.read()

# Replace root '/' to point to Welcome (Landing Page)
content = re.sub(
    r"Route::get\('/', \[PortalController::class, 'home'\]\)->name\('public\.home'\);",
    r"""Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
})->name('public.landing');""",
    content
)

# Remove the old /tentang-kami route that pointed to Welcome
old_tentang_kami = """// Public routes
Route::get('/tentang-kami', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
})->name('public.landing');
"""
content = content.replace(old_tentang_kami, "// Public routes\n")

# Make the old Portal / Home available at /portal or similar, OR update public.php
with open('/Users/user/Work/masjid-system/routes/web.php', 'w') as f:
    f.write(content)
