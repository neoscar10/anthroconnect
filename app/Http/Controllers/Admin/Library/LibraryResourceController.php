<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryResource;
use App\Models\LibraryResourceType;
use App\Models\Topic;
use App\Services\Library\LibraryAdminService;
use App\Services\Library\BookMetadataLookupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LibraryResourceController extends Controller
{
    protected $libraryService;
    protected $coverLookupService;

    public function __construct(LibraryAdminService $libraryService, BookMetadataLookupService $metadataLookupService)
    {
        $this->libraryService = $libraryService;
        $this->metadataLookupService = $metadataLookupService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'type_id']);
        $resources = $this->libraryService->listResourcesForAdmin($filters)->paginate(15)->withQueryString();
        
        $types = LibraryResourceType::active()->get();
        $topics = Topic::active()->get();

        return view('admin.library.resources.index', compact('resources', 'filters', 'types', 'topics'));
    }

    public function create()
    {
        return redirect()->route('admin.library.resources.index', ['open_modal' => 1]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author_display' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer',
            'resource_type_id' => 'required|exists:library_resource_types,id',
            'abstract' => 'required|string',
            'language' => 'nullable|string|max:100',
            'pages_count' => 'nullable|integer',
            'tags' => 'nullable|string',
            'access_type' => 'required|in:public,member_only',
            'status' => 'required|in:draft,published,archived',
            'topics' => 'nullable|array',
            'is_featured' => 'nullable',
            'is_recommended' => 'nullable',
            'is_editors_pick' => 'nullable',
            'allow_download' => 'nullable',
            'cover_source' => 'nullable|string',
            'cover_external_url' => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();
        
        // Handle Booleans
        foreach (['is_featured', 'is_recommended', 'is_editors_pick', 'allow_download'] as $field) {
            $data[$field] = $request->has($field);
        }
        
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $resource = $this->libraryService->createResource($data);

        // Handle cover (priority: manual upload > fetched cover)
        if ($request->hasFile('cover_image')) {
            $this->libraryService->handleFileUploads($resource, ['cover_image' => $request->file('cover_image')]);
            $resource->cover_source = 'upload';
            $resource->save();
        } elseif ($request->filled('fetched_cover_path')) {
            $resource->cover_image_path = $request->input('fetched_cover_path');
            $resource->cover_source = 'isbn_api';
            $resource->cover_external_url = $request->input('cover_external_url');
            $resource->save();
        }

        // Handle other files
        $this->libraryService->handleFileUploads($resource, $request->only(['resource_file', 'preview_file']));

        return redirect()->route('admin.library.resources.index')
            ->with('success', 'Library resource created successfully.');
    }

    public function edit(LibraryResource $resource)
    {
        $types = LibraryResourceType::active()->get();
        $topics = Topic::active()->get();
        
        $resource->load(['topics', 'tags']);
        $tagsString = $resource->tags->pluck('name')->implode(', ');

        return view('admin.library.resources.edit', compact('resource', 'types', 'topics', 'tagsString'));
    }

    public function update(Request $request, LibraryResource $resource)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author_display' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer',
            'resource_type_id' => 'required|exists:library_resource_types,id',
            'abstract' => 'required|string',
            'language' => 'nullable|string|max:100',
            'pages_count' => 'nullable|integer',
            'tags' => 'nullable|string',
            'access_type' => 'required|in:public,member_only',
            'status' => 'required|in:draft,published,archived',
            'topics' => 'nullable|array',
            'is_featured' => 'nullable',
            'is_recommended' => 'nullable',
            'is_editors_pick' => 'nullable',
            'allow_download' => 'nullable',
            'cover_source' => 'nullable|string',
            'cover_external_url' => 'nullable|string',
        ]);

        $data['updated_by'] = Auth::id();

        // Handle Booleans
        foreach (['is_featured', 'is_recommended', 'is_editors_pick', 'allow_download'] as $field) {
            $data[$field] = $request->has($field);
        }

        if ($data['status'] === 'published' && empty($resource->published_at)) {
            $data['published_at'] = now();
        }

        $this->libraryService->updateResource($resource, $data);

        // Handle cover (priority: manual upload > fetched cover)
        if ($request->hasFile('cover_image')) {
            $this->libraryService->handleFileUploads($resource, ['cover_image' => $request->file('cover_image')]);
            $resource->cover_source = 'upload';
            $resource->save();
        } elseif ($request->filled('fetched_cover_path')) {
            // Cleanup old cover if different
            if ($resource->cover_image_path && $resource->cover_image_path !== $request->input('fetched_cover_path')) {
                Storage::disk('public')->delete($resource->cover_image_path);
            }
            $resource->cover_image_path = $request->input('fetched_cover_path');
            $resource->cover_source = 'isbn_api';
            $resource->cover_external_url = $request->input('cover_external_url');
            $resource->save();
        }

        // Handle other files
        $this->libraryService->handleFileUploads($resource, $request->only(['resource_file', 'preview_file']));

        return redirect()->route('admin.library.resources.index')
            ->with('success', 'Library resource updated successfully.');
    }

    public function destroy(LibraryResource $resource)
    {
        $resource->delete();
        return redirect()->route('admin.library.resources.index')
            ->with('success', 'Resource moved to archive.');
    }

    /**
     * AJAX endpoint to lookup book metadata from ISBN.
     */
    public function lookupIsbn(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string'
        ]);

        $result = $this->metadataLookupService->lookupByIsbn($request->isbn);

        return response()->json($result);
    }
}
