<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortfolioItemController extends Controller
{
    public function store(Request $request, Portfolio $portfolio): RedirectResponse
    {
        abort_unless($portfolio->user_id === Auth::id(), 403);
        abort_if(!in_array($portfolio->status, ['draft', 'rejected']), 403, 'Cannot upload to submitted or approved portfolio');

        $data = $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(config('portfolio.item_types')))],
            'title' => ['nullable', 'string', 'max:255'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'max:' . config('portfolio.max_file_size')],
            'library_document_ids' => ['nullable', 'array'],
            'library_document_ids.*' => ['nullable', 'exists:faculty_documents,id'],
        ]);

        $uploadedCount = 0;
        $linkedCount = 0;
        $errors = [];

        // Handle library document selection
        if ($request->has('library_document_ids') && !empty($request->library_document_ids)) {
            foreach ($request->library_document_ids as $documentId) {
                $facultyDocument = \App\Models\FacultyDocument::where('id', $documentId)
                    ->where('user_id', Auth::id())
                    ->where('type', $data['type'])
                    ->first();

                if (!$facultyDocument) {
                    $errors[] = 'Selected library document not found or invalid.';
                    continue;
                }

                PortfolioItem::create([
                    'portfolio_id' => $portfolio->id,
                    'faculty_document_id' => $facultyDocument->id,
                    'type' => $data['type'],
                    'title' => $facultyDocument->title,
                    'file_path' => $facultyDocument->file_path, // Keep for backward compatibility
                    'metadata_json' => $facultyDocument->metadata_json,
                ]);

                $linkedCount++;
            }
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (!$file->isValid()) {
                    continue;
                }

                $extension = $file->getClientOriginalExtension();

                if (!in_array(strtolower($extension), config('portfolio.allowed_extensions'))) {
                    $errors[] = $file->getClientOriginalName() . ' - File type not allowed.';
                    continue;
                }

                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs(
                    "portfolios/{$portfolio->user_id}/{$portfolio->id}/{$data['type']}",
                    $fileName,
                    'local'
                );

                PortfolioItem::create([
                    'portfolio_id' => $portfolio->id,
                    'type' => $data['type'],
                    'title' => $data['title'] ?? $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'metadata_json' => [
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ],
                ]);

                $uploadedCount++;
            }
        }

        if (!empty($errors)) {
            $message = [];
            if ($uploadedCount > 0) $message[] = "{$uploadedCount} file(s) uploaded";
            if ($linkedCount > 0) $message[] = "{$linkedCount} document(s) linked from library";
            return back()->withErrors(['files' => $errors])->with('status', implode(', ', $message) . '.');
        }

        $message = [];
        if ($uploadedCount > 0) $message[] = "{$uploadedCount} file(s) uploaded";
        if ($linkedCount > 0) $message[] = "{$linkedCount} document(s) linked from library";
        return back()->with('status', implode(', ', $message) . ' successfully.');
    }

    public function download(Portfolio $portfolio, PortfolioItem $item): StreamedResponse
    {
        $user = Auth::user();

        // Allow access if:
        // 1. User is the portfolio owner, OR
        // 2. User is chair/admin/auditor reviewing the portfolio
        $canDownload = $portfolio->user_id === $user->id
            || in_array($user->role, ['chair', 'admin', 'auditor']);

        abort_unless($canDownload, 403, 'You do not have permission to download this file');
        abort_unless($item->portfolio_id === $portfolio->id, 404);

        // Use actual file path (from library if linked, otherwise from portfolio item)
        $filePath = $item->actual_file_path;

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $metadata = $item->metadata_json ?? [];
        $fileName = $metadata['original_name'] ?? basename($filePath);

        return Storage::disk('local')->download($filePath, $fileName);
    }

    public function preview(Portfolio $portfolio, PortfolioItem $item)
    {
        $user = Auth::user();

        // Allow access if:
        // 1. User is the portfolio owner, OR
        // 2. User is chair/admin/auditor reviewing the portfolio
        $canPreview = $portfolio->user_id === $user->id
            || in_array($user->role, ['chair', 'admin', 'auditor']);

        abort_unless($canPreview, 403, 'You do not have permission to preview this file');
        abort_unless($item->portfolio_id === $portfolio->id, 404);

        // Use actual file path (from library if linked, otherwise from portfolio item)
        $filePath = $item->actual_file_path;

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $metadata = $item->metadata_json ?? [];
        $fileName = $metadata['original_name'] ?? basename($filePath);
        $mimeType = $metadata['mime_type'] ?? Storage::disk('local')->mimeType($filePath);
        $fullPath = Storage::disk('local')->path($filePath);

        // For PDFs and images, serve inline for preview
        if (in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        // For other file types, still serve inline but browser may download
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function destroy(Portfolio $portfolio, PortfolioItem $item): RedirectResponse
    {
        abort_unless($portfolio->user_id === Auth::id(), 403);
        abort_unless($item->portfolio_id === $portfolio->id, 404);
        abort_if(!in_array($portfolio->status, ['draft', 'rejected']), 403, 'Cannot delete from submitted or approved portfolio');

        if (Storage::disk('local')->exists($item->file_path)) {
            Storage::disk('local')->delete($item->file_path);
        }

        $item->delete();

        return back()->with('status', 'File deleted successfully.');
    }

    public function update(Request $request, Portfolio $portfolio, PortfolioItem $item): RedirectResponse
    {
        $user = Auth::user();

        // Allow portfolio owner or chair/admin to update
        $canUpdate = $portfolio->user_id === $user->id || in_array($user->role, ['chair', 'admin']);
        abort_unless($canUpdate, 403, 'You do not have permission to update this document');
        abort_unless($item->portfolio_id === $portfolio->id, 404);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:' . config('portfolio.max_file_size')],
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        if (!in_array(strtolower($extension), config('portfolio.allowed_extensions'))) {
            return back()->withErrors(['file' => 'File type not allowed.']);
        }

        // Delete old file
        if (Storage::disk('local')->exists($item->file_path)) {
            Storage::disk('local')->delete($item->file_path);
        }

        // Upload new file
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs(
            "portfolios/{$portfolio->user_id}/{$portfolio->id}/{$item->type}",
            $fileName,
            'local'
        );

        // Update item
        $item->update([
            'file_path' => $filePath,
            'metadata_json' => [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ],
        ]);

        return back()->with('status', 'Document updated successfully.');
    }
}
