<?php

namespace App\Http\Controllers;

use App\Services\Explore\ExplorePublicService;
use Illuminate\Http\Request;

class PublicExploreController extends Controller
{
    protected ExplorePublicService $exploreService;

    public function __construct(ExplorePublicService $exploreService)
    {
        $this->exploreService = $exploreService;
    }

    public function index(Request $request)
    {
        $tagId = $request->query('tag_id');
        $filters = ['tag_id' => $tagId];

        $tagGroups = $this->exploreService->getPublicTagGroups();
        $featuredArticle = $this->exploreService->getFeaturedArticle($tagId);
        
        $articles = $this->exploreService->getPublishedArticles($filters);
        
        $articles->appends($request->all());

        return view('pages.explore', compact('tagGroups', 'featuredArticle', 'articles', 'tagId'));
    }

    public function show(string $slug)
    {
        $article = $this->exploreService->getArticleBySlug($slug);

        if (!$article) {
            abort(404);
        }

        // Protection: Redirect if unauthorized
        if (!$article->canAccess(auth()->user())) {
            return redirect()->route('explore.index')
                ->with('error', 'This narrative is reserved for the AnthroConnect Scholar community. Please upgrade your membership to unlock full access.');
        }

        $relatedArticles = $this->exploreService->getRelatedArticles($article, 2);

        return view('pages.explore-detail', compact('article', 'relatedArticles'));
    }
}
