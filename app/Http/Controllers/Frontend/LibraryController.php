<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LibraryResource;
use App\Services\Library\LibraryFrontendService;
use App\Services\Library\LibraryAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LibraryController extends Controller
{
    public function __construct(
        protected LibraryFrontendService $libraryService,
        protected LibraryAccessService $accessService
    ) {
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'type' => $request->string('type')->toString(),
            'region' => $request->string('region')->toString(),
            'year' => $request->string('year')->toString(),
            'topic' => $request->string('topic')->toString(),
            'sort' => $request->string('sort')->toString() ?: 'latest',
        ];

        $user = Auth::user();

        return view('frontend.library.index', [
            'featuredResources' => $this->libraryService->getFeaturedResources(3),
            'latestResources' => $this->libraryService->getLatestResources(6),
            'recommendedResources' => $this->libraryService->getRecommendedResources($user, 3),
            'topics' => $this->libraryService->getBrowseTopics(8),
            'resourceTypes' => $this->libraryService->getResourceTypes(),
            'regions' => $this->libraryService->getRegions(),
            'publicationYears' => $this->libraryService->getPublicationYears(),
            'resources' => $this->libraryService->searchResources($filters, 12),
            'filters' => $filters,
            'accessService' => $this->accessService,
        ]);
    }

    public function show(LibraryResource $resource)
    {
        abort_unless($this->libraryService->isPublished($resource), 404);

        $user = Auth::user();
        $access = $this->accessService->check($user, $resource);

        return view('frontend.library.show', [
            'resource' => $resource->load([
                'resourceType',
                'region',
                'topics',
                'tags',
            ]),
            'access' => $access,
            'relatedResources' => $this->libraryService->getRelatedResources($resource, 4),
            'relatedLearningItems' => $this->libraryService->getRelatedLearning($resource),
            'relatedDiscussions' => $this->libraryService->getRelatedDiscussions($resource),
        ]);
    }

    public function download(LibraryResource $resource): StreamedResponse
    {
        abort_unless($this->libraryService->isPublished($resource), 404);

        $access = $this->accessService->check(Auth::user(), $resource);

        abort_unless($access['allowed'], 403);
        abort_unless((bool) $resource->allow_download, 403);
        abort_unless(!empty($resource->file_path), 404);
        abort_unless(Storage::disk('public')->exists($resource->file_path), 404);

        $fileName = str($resource->title)->slug()->append('.pdf')->toString();

        return Storage::disk('public')->download($resource->file_path, $fileName);
    }
}
