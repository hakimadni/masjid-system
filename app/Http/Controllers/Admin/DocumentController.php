<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Mosque;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    public function index(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $query = Document::where('mosque_id', $mosque->id)
            ->with('creator')
            ->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $documents = $query->paginate(20);

        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
            'filters' => $request->only(['category', 'status', 'search']),
            'categories' => Document::where('mosque_id', $mosque->id)
                ->distinct()
                ->pluck('category'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $fileData = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileData = [
                'file_path' => $file->store('documents', 'public'),
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ];
        }

        $document = Document::create([
            'mosque_id' => $mosque->id,
            'created_by' => auth()->id(),
            ...$validated,
            ...$fileData ?? [],
        ]);

        $this->auditLogService->log('document.created', $document, null, $document->toArray());

        return back()->with('success', 'Dokumen berhasil disimpan.');
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $before = $document->toArray();

        $fileData = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileData = [
                'file_path' => $file->store('documents', 'public'),
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ];
        }

        $document->update([
            ...$validated,
            ...$fileData ?? [],
        ]);

        $this->auditLogService->log('document.updated', $document, $before, $document->fresh()?->toArray());

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $before = $document->toArray();
        $document->delete();

        $this->auditLogService->log('document.deleted', $document, $before, null);

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function show(Document $document): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();
        abort_if($document->mosque_id !== $mosque->id, 403);

        return Inertia::render('Admin/Documents/Show', [
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'category' => $document->category,
                'document_number' => $document->document_number,
                'document_date' => $document->document_date,
                'description' => $document->description,
                'file_path' => $document->file_path ? Storage::url($document->file_path) : null,
                'file_name' => $document->file_name,
                'file_size' => $document->file_size,
                'status' => $document->status,
                'creator' => $document->creator ? ['name' => $document->creator->name] : null,
            ]
        ]);
    }

    public function download(Document $document): \Symfony\Component\HttpFoundation\BinaryFileResponse|RedirectResponse
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();
        abort_if($document->mosque_id !== $mosque->id, 403);

        if (!$document->file_path || !Storage::exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->file(storage_path('app/' . $document->file_path), [
            'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"',
        ]);
    }
}