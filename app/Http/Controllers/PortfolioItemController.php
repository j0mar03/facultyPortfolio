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
        abort_if($portfolio->status !== 'draft', 403, 'Cannot upload to submitted portfolio');

        $data = $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(config('portfolio.item_types')))],
            'title' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:' . config('portfolio.max_file_size')],
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        if (!in_array(strtolower($extension), config('portfolio.allowed_extensions'))) {
            return back()->withErrors(['file' => 'File type not allowed.']);
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
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

        return back()->with('status', 'File uploaded successfully.');
    }

    public function download(Portfolio $portfolio, PortfolioItem $item): StreamedResponse
    {
        abort_unless($portfolio->user_id === Auth::id(), 403);
        abort_unless($item->portfolio_id === $portfolio->id, 404);

        if (!Storage::disk('local')->exists($item->file_path)) {
            abort(404, 'File not found');
        }

        $metadata = $item->metadata_json ?? [];
        $fileName = $metadata['original_name'] ?? basename($item->file_path);

        return Storage::disk('local')->download($item->file_path, $fileName);
    }

    public function destroy(Portfolio $portfolio, PortfolioItem $item): RedirectResponse
    {
        abort_unless($portfolio->user_id === Auth::id(), 403);
        abort_unless($item->portfolio_id === $portfolio->id, 404);
        abort_if($portfolio->status !== 'draft', 403, 'Cannot delete from submitted portfolio');

        if (Storage::disk('local')->exists($item->file_path)) {
            Storage::disk('local')->delete($item->file_path);
        }

        $item->delete();

        return back()->with('status', 'File deleted successfully.');
    }
}
