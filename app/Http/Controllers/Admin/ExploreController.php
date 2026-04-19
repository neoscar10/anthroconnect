<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExploreArticle;
use App\Models\Topic;
use App\Services\Explore\ExploreArticleService;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    protected ExploreArticleService $articleService;

    public function __construct(ExploreArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search', ''),
            'topic_id' => $request->query('topic_filter_id', ''),
            'status' => $request->query('status_filter', ''),
        ];

        $articles = $this->articleService->listArticlesForAdmin($filters)->paginate(15);
        $articles->appends($request->all());

        $topics = Topic::orderBy('name')->active()->get();

        $stats = [
            'total' => ExploreArticle::count(),
            'published' => ExploreArticle::published()->count(),
            'drafts' => ExploreArticle::where('status', 'draft')->count(),
            'featured' => ExploreArticle::featured()->count(),
        ];

        return view('admin.explore.index', compact('articles', 'topics', 'stats', 'filters'))
            ->with('title', 'Explore Content Management');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:explore_articles,slug',
            'excerpt' => 'required|string|max:500',
            'markdown_content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        $this->articleService->createArticle($validated);

        return redirect()->route('admin.explore.index')
            ->with('success', 'Archive narrative created successfully.');
    }

    public function update(Request $request, ExploreArticle $exploreArticle)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:explore_articles,slug,' . $exploreArticle->id,
            'excerpt' => 'required|string|max:500',
            'markdown_content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        $this->articleService->updateArticle($exploreArticle, $validated);

        return redirect()->route('admin.explore.index')
            ->with('success', 'Archive narrative updated successfully.');
    }

    public function destroy(ExploreArticle $exploreArticle)
    {
        try {
            $exploreArticle->delete();
            return redirect()->route('admin.explore.index')
                ->with('success', 'Article moved to trash.');
        } catch (\Exception $e) {
            return redirect()->route('admin.explore.index')
                ->with('error', $e->getMessage());
        }
    }

    public function toggleFeatured(ExploreArticle $exploreArticle)
    {
        $exploreArticle->update(['is_featured' => !$exploreArticle->is_featured]);
        
        return redirect()->back()
            ->with('success', 'Narrative featured status updated.');
    }
}
