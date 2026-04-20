<?php

namespace App\Livewire\Admin\Explore;

use App\Models\ExploreArticle;
use App\Models\Topic;
use App\Services\Explore\ExploreArticleService;
use App\Services\Topic\TopicService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class ExploreIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public $search = '';
    public $topic_filter_id = '';
    public $status_filter = '';
    public $access_filter = '';

    // Article Modal State
    public ?ExploreArticle $editingArticle = null;
    public $modalSessionId = ''; // Used to re-render Markdown editor

    // Article Form fields
    public $topic_id = '';
    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $markdown_content = '';
    public $status = 'draft';
    public $is_featured = false;
    public $is_members_only = false;
    public $featured_image = null; 
    public $existing_image = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'topic_filter_id' => ['as' => 'topic', 'except' => ''],
        'status_filter' => ['as' => 'status', 'except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTitle($value)
    {
        if (!$this->editingArticle || empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    /**
     * Article Modal Logic
     */
    public function openArticleModal($id = null)
    {
        $this->resetErrorBag();
        $this->reset('featured_image', 'existing_image');
        $this->modalSessionId = uniqid(); // Force fresh editor state

        if ($id) {
            $this->editingArticle = ExploreArticle::find($id);
            $this->topic_id = $this->editingArticle->topic_id;
            $this->title = $this->editingArticle->title;
            $this->slug = $this->editingArticle->slug;
            $this->excerpt = $this->editingArticle->excerpt;
            $this->markdown_content = $this->editingArticle->markdown_content;
            $this->status = $this->editingArticle->status;
            $this->is_featured = $this->editingArticle->is_featured;
            $this->is_members_only = $this->editingArticle->is_members_only;
            $this->existing_image = $this->editingArticle->featured_image;
        } else {
            $this->editingArticle = null;
            $this->reset('topic_id', 'title', 'slug', 'excerpt', 'markdown_content', 'status', 'is_featured', 'is_members_only');
            $this->status = 'draft';
        }
    }

    public function closeArticleModal()
    {
        $this->reset('topic_id', 'title', 'slug', 'excerpt', 'markdown_content', 'status', 'is_featured', 'is_members_only', 'featured_image', 'existing_image', 'editingArticle');
        $this->resetErrorBag();
    }

    public function saveArticle(ExploreArticleService $service)
    {
        // Defensive slug generation
        if (empty($this->slug)) {
            $this->slug = \Illuminate\Support\Str::slug($this->title);
        }

        $this->validate([
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:explore_articles,slug,' . ($this->editingArticle?->id ?? 'NULL'),
            'excerpt' => 'required|string|max:500',
            'markdown_content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'is_members_only' => 'boolean',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'topic_id' => $this->topic_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'markdown_content' => $this->markdown_content,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_members_only' => $this->is_members_only,
            'featured_image' => $this->featured_image,
        ];

        if ($this->editingArticle) {
            $service->updateArticle($this->editingArticle, $data);
            session()->flash('success', 'Archive narrative updated successfully.');
            $this->dispatch('article-saved');
        } else {
            $service->createArticle($data);
            session()->flash('success', 'Archive narrative created successfully.');
            $this->dispatch('article-saved');
        }

        $this->closeArticleModal();
    }

    /**
     * Quick Actions
     */
    public function toggleFeatured($id)
    {
        $article = ExploreArticle::findOrFail($id);
        $article->update(['is_featured' => !$article->is_featured]);
    }

    public function toggleMembersOnly($id)
    {
        $article = ExploreArticle::findOrFail($id);
        $article->update(['is_members_only' => !$article->is_members_only]);
        $this->dispatch('notify', ['message' => 'Article access updated.']);
    }

    public function deleteArticle($id)
    {
        $article = ExploreArticle::findOrFail($id);
        $article->delete();
        $this->dispatch('notify', ['message' => 'Article moved to trash.']);
    }

    public function render(ExploreArticleService $articleService, TopicService $topicService)
    {
        $articles = $articleService->listArticlesForAdmin([
            'search' => $this->search,
            'topic_id' => $this->topic_filter_id,
            'status' => $this->status_filter,
            'is_members_only' => $this->access_filter === 'members' ? true : ($this->access_filter === 'public' ? false : null),
        ])->paginate(15);

        $topics = Topic::orderBy('name')->active()->get();
        
        $stats = [
            'total' => ExploreArticle::count(),
            'published' => ExploreArticle::published()->count(),
            'drafts' => ExploreArticle::where('status', 'draft')->count(),
            'featured' => ExploreArticle::featured()->count(),
        ];

        return view('livewire.admin.explore.explore-index', [
            'articles' => $articles,
            'topics' => $topics,
            'stats' => $stats,
        ])->layout('layouts.admin', ['title' => 'Explore Content Management']);
    }
}
