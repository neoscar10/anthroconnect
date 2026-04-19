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
        $topicId = $request->query('topic_id');
        $filters = ['topic_id' => $topicId];

        $topics = $this->exploreService->getPublicTopics();
        $featuredArticle = $this->exploreService->getFeaturedArticle($topicId);
        
        $articles = $this->exploreService->getPublishedArticles($filters);
        
        $articles->appends($request->all());

        return view('pages.explore', compact('topics', 'featuredArticle', 'articles', 'topicId'));
    }

    public function show(string $slug)
    {
        $article = $this->exploreService->getArticleBySlug($slug);

        if (!$article) {
            abort(404);
        }

        $relatedArticles = $this->exploreService->getRelatedArticles($article, 2);

        return view('pages.explore-detail', compact('article', 'relatedArticles'));
    }
}
