<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Asset;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

class PlaceholderModuleController extends Controller
{
    public function events(): Response
    {
        return $this->renderModule(
            'Kegiatan',
            'Kelola agenda kajian, program sosial, dan agenda besar masjid.',
            Event::query()->count(),
            [
                'Mulai dengan form CRUD agenda dan status publikasi.',
                'Hubungkan PIC, lokasi, dan reminder operasional.',
                'Tampilkan kegiatan terdekat di dashboard.',
            ]
        );
    }

    public function announcements(): Response
    {
        return $this->renderModule(
            'Pengumuman',
            'Publikasikan info penting, perubahan jadwal, dan pesan internal pengurus.',
            Announcement::query()->count(),
            [
                'Tambahkan editor konten dan jadwal publikasi.',
                'Sediakan status draft, published, dan archived.',
                'Hubungkan pengumuman aktif ke dashboard.',
            ]
        );
    }

    public function assets(): Response
    {
        return $this->renderModule(
            'Inventaris',
            'Pantau aset, perlengkapan ibadah, dan kebutuhan pemeliharaan.',
            Asset::query()->count(),
            [
                'Lengkapi form barang, lokasi, kondisi, dan status perawatan.',
                'Hubungkan issue operasional marbot ke data aset.',
                'Tambahkan filter kategori dan kondisi.',
            ]
        );
    }

    public function reports(): Response
    {
        return $this->renderModule(
            'Laporan',
            'Konsolidasikan ringkasan keuangan, donasi, jadwal, dan kegiatan.',
            0,
            [
                'Bangun filter periode dan kategori laporan.',
                'Tambahkan ringkasan KPI untuk bendahara dan ketua DKM.',
                'Siapkan ekspor PDF/Excel di fase berikutnya.',
            ]
        );
    }

    public function settings(): Response
    {
        return $this->renderModule(
            'Pengaturan',
            'Atur identitas masjid, struktur peran, dan preferensi operasional.',
            0,
            [
                'Buat form identitas masjid dan kontak resmi.',
                'Tambahkan panel role mapping dan hak akses dasar.',
                'Sediakan selector masjid saat multi-masjid berkembang.',
            ]
        );
    }

    public function jamaah(): Response
    {
        return $this->renderModule(
            'Jamaah',
            'Siapkan data jamaah untuk segmentasi komunitas dan kehadiran kegiatan.',
            0,
            [
                'Buat master data jamaah dan keluarga inti.',
                'Hubungkan ke kegiatan, donasi, dan komunikasi jamaah.',
                'Tambahkan pencarian dan status keaktifan.',
            ]
        );
    }

    public function documents(): Response
    {
        return $this->renderModule(
            'Dokumen',
            'Arsipkan proposal, surat menyurat, notulen, dan file administratif penting.',
            0,
            [
                'Tambahkan unggah file dan kategori dokumen.',
                'Sediakan status aktif dan arsip.',
                'Hubungkan dokumen ke kegiatan atau pengumuman.',
            ]
        );
    }

    private function renderModule(string $title, string $description, int $records, array $nextSteps): Response
    {
        return Inertia::render('Admin/Modules/Placeholder', [
            'module' => [
                'label' => $title,
                'description' => $description,
                'records' => $records,
                'next_steps' => $nextSteps,
            ],
        ]);
    }
}
