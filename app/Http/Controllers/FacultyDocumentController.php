<?php

namespace App\Http\Controllers;

use App\Models\FacultyDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FacultyDocumentController extends Controller
{
    /**
     * Display the faculty's document library
     */
    public function index(Request $request): View
    {
        abort_unless(Auth::user()->role === 'faculty', 403);

        $type = $request->get('type');
        $query = FacultyDocument::where('user_id', Auth::id());

        if ($type) {
            $query->where('type', $type);
        }

        $documents = $query->with('portfolioItems')->orderBy('type')->orderBy('created_at', 'desc')->paginate(20);
        $itemTypes = config('portfolio.item_types');

        // Filter to only show reusable document types
        $reusableTypes = ['sample_quiz', 'major_exam', 'tos', 'activity_rubrics'];

        return view('faculty.documents.index', compact('documents', 'itemTypes', 'type', 'reusableTypes'));
    }

    /**
     * Store a new document in the library
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'faculty', 403);

        $data = $request->validate([
            'type' => ['required', 'string', 'in:sample_quiz,major_exam,tos,activity_rubrics'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'subject_code' => ['nullable', 'string', 'max:50'],
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . uniqid() . '_' . $originalName;
        $filePath = 'faculty-documents/' . Auth::id() . '/' . $data['type'] . '/' . $fileName;

        Storage::disk('local')->put($filePath, file_get_contents($file));

        FacultyDocument::create([
            'user_id' => Auth::id(),
            'type' => $data['type'],
            'title' => $data['title'],
            'file_path' => $filePath,
            'metadata_json' => [
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'original_name' => $originalName,
            ],
            'subject_code' => $data['subject_code'] ?? null,
        ]);

        return back()->with('status', 'Document added to library successfully!');
    }

    /**
     * Delete a document from the library
     */
    public function destroy(FacultyDocument $facultyDocument): RedirectResponse
    {
        abort_unless($facultyDocument->user_id === Auth::id(), 403);
        abort_unless(Auth::user()->role === 'faculty', 403);

        // Check if document is being used in any portfolios
        if ($facultyDocument->portfolioItems()->count() > 0) {
            return back()->withErrors([
                'delete' => 'Cannot delete document that is being used in portfolios. Please remove it from portfolios first.'
            ]);
        }

        // Delete the file
        if (Storage::disk('local')->exists($facultyDocument->file_path)) {
            Storage::disk('local')->delete($facultyDocument->file_path);
        }

        $facultyDocument->delete();

        return back()->with('status', 'Document deleted from library successfully!');
    }

    /**
     * Download a document from the library
     */
    public function download(FacultyDocument $facultyDocument): StreamedResponse
    {
        abort_unless($facultyDocument->user_id === Auth::id(), 403);
        abort_unless(Auth::user()->role === 'faculty', 403);

        if (!Storage::disk('local')->exists($facultyDocument->file_path)) {
            abort(404, 'File not found');
        }

        $metadata = $facultyDocument->metadata_json ?? [];
        $fileName = $metadata['original_name'] ?? basename($facultyDocument->file_path);

        return Storage::disk('local')->download($facultyDocument->file_path, $fileName);
    }
}
