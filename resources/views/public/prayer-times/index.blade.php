<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Sholat - {{ $mosque->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-indigo-800">
                            Jadwal Sholat Hari Ini
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $mosque->name }} • {{ now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        <i class="fas fa-home me-2"></i> Dashboard Admin
                    </a>
                </div>
            </div>
        </header>

        @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                {{ session('error') }}
            </div>
        </div>
        @endif

        @if($todayPrayerTimes)
        <!-- Today's Prayer Times -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-8">
                    <h2 class="text-xl font-semibold text-indigo-800 mb-6">
                        Jadwal Sholat Hari Ini
                    </h2>
                    
                    <div class="grid gap-4">
                        @foreach(['imsak' => 'Imsak', 'subuh' => 'Subuh', 'terbit' => 'Terbit', 'dhuha' => 'Dhuha', 'dzuhur' => 'Dzuhur', 'ashar' => 'Ashar', 'maghrib' => 'Maghrib', 'isya' => 'Isya'] as $key => $name)
                        <div class="bg-indigo-50 rounded-xl p-4 flex items-center justify-between">
                            <span class="font-medium text-indigo-800">{{ $name }}</span>
                            <span class="text-2xl font-bold text-indigo-600">{{ $todayPrayerTimes[$key] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Monthly Prayer Times -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-8">
                    <h2 class="text-xl font-semibold text-indigo-800 mb-6">
                        Jadwal Sholat Bulan Ini
                    </h2>
                    
                    @if(!empty($prayerTimes['jadwal']))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-600 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-600 uppercase tracking-wider">
                                        Imsak
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-600 uppercase tracking-wider">
                                        Subuh
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-600 uppercase tracking-wider">
                                        Dzuhur
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-600 uppercase tracking-wider">
                                        Ashar
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-600 uppercase tracking-wider">
                                        Maghrib
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-600 uppercase tracking-wider">
                                        Isya
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($prayerTimes['jadwal'] as $date => $times)
                                <tr class="hover:bg-indigo-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d M') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $times['imsak'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $times['subuh'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $times['dzuhur'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $times['ashar'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $times['maghrib'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $times['isya'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="py-8 text-center text-gray-500">
                        Tidak ada data jadwal sholat untuk bulan ini.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Media Section -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-8">
                    <h2 class="text-xl font-semibold text-indigo-800 mb-6">
                        Media dan Pengajian
                    </h2>
                    
                    @if($media->isNotEmpty())
                    <div class="space-y-6">
                        @foreach($media as $item)
                        <div class="border rounded-lg overflow-hidden shadow-sm">
                            @if($item->type === 'youtube' && Str::contains($item->url, 'youtube.com'))
                            <div class="relative w-full h-0 pb-[56.25%]">
                                <iframe 
                                    class="absolute inset-0"
                                    src="{{ Str::replace('watch?v=', 'embed/', $item->url) }}"
                                    title="{{ $item->title }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            @elseif($item->type === 'image')
                            <img src="{{ $item->url }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                            @else
                            <div class="bg-gray-50 p-4">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-file text-indigo-400"></div>
                                    <div>
                                        <h3 class="font-medium text-indigo-800">{{ $item->title }}</h3>
                                        <p class="text-sm text-gray-500">{{ $item->type }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="p-4">
                                <h3 class="font-medium text-indigo-800 mb-2">{{ $item->title }}</h3>
                                @if($item->description)
                                <p class="text-sm text-gray-600 mb-3">{{ $item->description }}</p>
                                @endif
                                <a href="{{ $item->url }}" 
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full hover:bg-indigo-200">
                                    <i class="fas fa-external-link-alt me-1"></i> Buka
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="py-8 text-center text-gray-500">
                        <i class="fas fa-video text-indigo-200 mb-4"></i>
                        <p>Belum ada media yang ditambahkan.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Finance Summary -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-8">
                    <h2 class="text-xl font-semibold text-indigo-800 mb-6>
                        Laporan Keuangan (30 Hari Terakhir)
                    </h2>
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="bg-indigo-50 rounded-xl p-4">
                            <h3 class="font-semibold text-indigo-800 mb-2">Pemasukan</h3>
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ isset($financeSummary['income']['total']) ? number_format($financeSummary['income']['total'], 0, ',', '.') : '0' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ isset($financeSummary['income']['count']) ? $financeSummary['income']['count'] : 0 }} transaksi
                            </p>
                        </div>
                        
                        <div class="bg-indigo-50 rounded-xl p-4">
                            <h3 class="font-semibold text-indigo-800 mb-2">Pengeluaran</h3>
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ isset($financeSummary['expense']['total']) ? number_format($financeSummary['expense']['total'], 0, ',', '.') : '0' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ isset($financeSummary['expense']['count']) ? $financeSummary['expense']['count'] : 0 }} transaksi
                            </p>
                        </div>
                        
                        <div class="bg-indigo-50 rounded-xl p-4">
                            <h3 class="font-semibold text-indigo-800 mb-2">Saldo Kas</h3>
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ 
                                    $income = isset($financeSummary['income']['total']) ? $financeSummary['income']['total'] : 0;
                                    $expense = isset($financeSummary['expense']['total']) ? $financeSummary['expense']['total'] : 0;
                                    echo number_format($income - $expense, 0, ',', '.');
                                }}
                            </p>
                            <p class="text-sm text-gray-500">Seluruh transaksi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-8">
                    <h2 class="text-xl font-semibold text-indigo-800 mb-6">
                        Kegiatan Terdekat
                    </h2>
                    
                    @if($upcomingEvents->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($upcomingEvents as $event)
                        <div class="border-l-4 border-indigo-500 bg-indigo-50 p-4 rounded-r-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-indigo-800">{{ $event->title }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $event->location ?? '-' }}
                                    </p>
                                    @if($event->pic_name)
                                    <p class="text-sm text-gray-600 mb-2">
                                        <i class="fas fa-user me-1"></i> {{ $event->pic_name }}
                                    </p>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <p class="text-xs font-medium text-indigo-600 uppercase">
                                        {{ \Carbon\Carbon::parse($event->start_at)->translatedFormat('d') }}
                                    </p>
                                    <p class="text-2xl font-bold text-indigo-600">
                                        {{ \Carbon\Carbon::parse($event->start_at)->translatedFormat('M') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="py-8 text-center text-gray-500">
                        <i class="fas fa-calendar-alt text-indigo-200 mb-4"></i>
                        <p>Belum ada kegiatan yang dijadwalkan.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-gray-500">
            &copy; {{ now()->year }} {{ $mosque->name }}. Hak cipta dilindungi undang-undang.
        </div>
    </footer>
</body>
</html>