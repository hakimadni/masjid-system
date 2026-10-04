import re

with open('/Users/user/Work/masjid-system/routes/public.php', 'r') as f:
    content = f.read()

# Change the Portal route from '/' to '/portal'
content = content.replace("Route::get('/', [PortalController::class, 'home'])->name('public.home');", 
                          "Route::get('/portal', [PortalController::class, 'home'])->name('public.home');")

with open('/Users/user/Work/masjid-system/routes/public.php', 'w') as f:
    f.write(content)
