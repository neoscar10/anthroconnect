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
        
        $excludeId = $featuredArticle ? $featuredArticle->id : null;
        $articles = $this->exploreService->getPublishedArticles($filters, $excludeId);
        
        $articles->appends($request->all());

        return view('pages.explore', compact('topics', 'featuredArticle', 'articles', 'topicId'));
    }
}
