<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Event;
use App\Models\QurbanSaving;
use App\Models\ZakatMuzakki;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Notifications/Index', [
            'templates' => [
                'kajian' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\nKajian rutin masjid akan segera dimulai.\n\n📅 Tanggal: {date}\n🕐 Waktu: {time}\n📍 Tempat: {location}\n🎤 Ustadz: {speaker}\n\nKami harapkan kehadiran Bapak/Ibu yang budiman.",
                'jumat' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\n📅 Pengingat: Shalat Jumat akan dimulai pukul 12.00 WIB.\n\nKami harapkan kehadiran Bapak/Ibu yang budiman untuk menunaikan shalat Jumat berjamaah.",
                'donasi' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\nMasjid kami membutuhkan dukungan Bapak/Ibu untuk pembangunan/infrastruktur masjid.\n\n💳 Rekening: {bank_account}\n📱 QRIS tersedia\n\nSemoga amal jariyah Bapak/Ibu diterima Allah SWT.",
                'qurban' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\nProgram Qurban tahun ini telah dibuka.\n\n🐄 Sapi: Rp {cattle_price}\n🐑 Kambing: Rp {goat_price}\n\nPendaftaran sampai {deadline}. Hubungi panitia untuk informasi lebih lanjut.",
                'zakat' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\nProgram Zakat Fitrah dan Zakat Mal masjid kami telah dibuka.\n\n📅 Periode: {period}\n📍 Lokasi: {location}\n\nZakat yang sudah dikumpulkan akan disalurkan ke mustahik yang berhak.",
                'emergency' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\n{pengumuman}\n\nMohon perhatian dan bantuannya.",
            ],
        ]);
    }

    public function generate(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:kajian,jumat,donasi,qurban,zakat,emergency'],
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'speaker' => ['nullable', 'string'],
            'bank_account' => ['nullable', 'string'],
            'cattle_price' => ['nullable', 'string'],
            'goat_price' => ['nullable', 'string'],
            'deadline' => ['nullable', 'string'],
            'period' => ['nullable', 'string'],
            'pengumuman' => ['nullable', 'string'],
        ]);

        $templates = $this->getTemplates();
        $template = $templates[$validated['type']] ?? '';

        $placeholders = [
            '{date}' => $validated['date'] ?? '-',
            '{time}' => $validated['time'] ?? '-',
            '{location}' => $validated['location'] ?? '-',
            '{speaker}' => $validated['speaker'] ?? '-',
            '{bank_account}' => $validated['bank_account'] ?? '-',
            '{cattle_price}' => $validated['cattle_price'] ?? '-',
            '{goat_price}' => $validated['goat_price'] ?? '-',
            '{deadline}' => $validated['deadline'] ?? '-',
            '{period}' => $validated['period'] ?? '-',
            '{pengumuman}' => $validated['pengumuman'] ?? '-',
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $template);

        return response()->json(['message' => $message]);
    }

    private function getTemplates(): array
    {
        return [
            'kajian' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\nKajian rutin masjid akan segera dimulai.\n\n📅 Tanggal: {date}\n🕐 Waktu: {time}\n📍 Tempat: {location}\n🎤 Ustadz: {speaker}\n\nKami harapkan kehadiran Bapak/Ibu yang budiman.",
            'jumat' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\n📅 Pengingat: Shalat Jumat akan dimulai pukul 12.00 WIB.\n\nKami harapkan kehadiran Bapak/Ibu yang budiman untuk menunaikan shalat Jumat berjamaah.",
            'donasi' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\nMasjid kami membutuhkan dukungan Bapak/Ibu untuk pembangunan/infrastruktur masjid.\n\n💳 Rekening: {bank_account}\n📱 QRIS tersedia\n\nSemoga amal jariyah Bapak/Ibu diterima Allah SWT.",
            'qurban' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\nProgram Qurban tahun ini telah dibuka.\n\n🐄 Sapi: Rp {cattle_price}\n🐑 Kambing: Rp {goat_price}\n\nPendaftaran sampai {deadline}. Hubungi panitia untuk informasi lebih lanjut.",
            'zakat' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\nProgram Zakat Fitrah dan Zakat Mal masjid kami telah dibuka.\n\n📅 Periode: {period}\n📍 Lokasi: {location}\n\nZakat yang sudah dikumpulkan akan disalurkan ke mustahik yang berhak.",
            'emergency' => "Assalamu'alaikum warahmatullahi wabarakatuh\n\n{pengumuman}\n\nMohon perhatian dan bantuannya.",
        ];
    }
}