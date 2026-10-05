import re

with open('/Users/user/Work/masjid-system/app/Http/Controllers/Admin/ScheduleController.php', 'r') as f:
    content = f.read()

# For Prayer
old_prayer = """        PrayerSchedule::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            'schedule_date' => $validated['schedule_date'],
            'prayer_name' => $validated['prayer_name'],
            'prayer_time' => $validated['prayer_time'] ?? null,
            'imam_name' => $validated['imam_name'] ?? null,
            'muadzin_name' => $validated['muadzin_name'] ?? null,
            'khatib_name' => $validated['khatib_name'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);"""

new_prayer = """        $recurrence = $validated['recurrence'] ?? 'none';
        $iterations = 1;
        if ($recurrence === 'daily') $iterations = 30;
        if ($recurrence === 'weekly') $iterations = 4;
        if ($recurrence === 'monthly') $iterations = 3;

        $baseDate = \Carbon\Carbon::parse($validated['schedule_date']);

        for ($i = 0; $i < $iterations; $i++) {
            $date = $baseDate->copy();
            if ($recurrence === 'daily') $date->addDays($i);
            if ($recurrence === 'weekly') $date->addWeeks($i);
            if ($recurrence === 'monthly') $date->addMonths($i);

            PrayerSchedule::query()->create([
                'mosque_id' => $request->user()->mosque_id,
                'schedule_date' => $date->toDateString(),
                'prayer_name' => $validated['prayer_name'],
                'prayer_time' => $validated['prayer_time'] ?? null,
                'imam_name' => $validated['imam_name'] ?? null,
                'muadzin_name' => $validated['muadzin_name'] ?? null,
                'khatib_name' => $validated['khatib_name'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
        }"""

content = content.replace(old_prayer, new_prayer)

# For Service
old_service = """        ServiceSchedule::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            'title' => $validated['title'],
            'role_type' => $validated['role_type'],
            'person_name' => $validated['person_name'],
            'location' => $validated['location'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);"""

new_service = """        $recurrence = $validated['recurrence'] ?? 'none';
        $iterations = 1;
        if ($recurrence === 'daily') $iterations = 30;
        if ($recurrence === 'weekly') $iterations = 4;
        if ($recurrence === 'monthly') $iterations = 3;

        $baseDate = \Carbon\Carbon::parse($validated['scheduled_at']);

        for ($i = 0; $i < $iterations; $i++) {
            $date = $baseDate->copy();
            if ($recurrence === 'daily') $date->addDays($i);
            if ($recurrence === 'weekly') $date->addWeeks($i);
            if ($recurrence === 'monthly') $date->addMonths($i);

            ServiceSchedule::query()->create([
                'mosque_id' => $request->user()->mosque_id,
                'title' => $validated['title'],
                'role_type' => $validated['role_type'],
                'person_name' => $validated['person_name'],
                'location' => $validated['location'] ?? null,
                'scheduled_at' => $date->toDateTimeString(),
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
        }"""

content = content.replace(old_service, new_service)

with open('/Users/user/Work/masjid-system/app/Http/Controllers/Admin/ScheduleController.php', 'w') as f:
    f.write(content)

