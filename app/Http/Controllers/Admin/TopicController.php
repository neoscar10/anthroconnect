<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Services\Topic\TopicService;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    protected TopicService $topicService;

    public function __construct(TopicService $topicService)
    {
        $this->topicService = $topicService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search', ''),
            'status' => $request->query('status', '')
        ];

        $serviceFilters = ['search' => $filters['search']];
        if ($filters['status'] !== '') {
            $serviceFilters['is_active'] = $filters['status'] === 'active';
        }

        $topics = $this->topicService->listTopicsForAdmin($serviceFilters)->paginate(5);
        $topics->appends($request->all());

        return view('admin.topics.index', compact('topics', 'filters'))
            ->with('title', 'Topics Management');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:topics,slug',
            'short_description' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_members_only'] = $request->has('is_members_only');

        $this->topicService->createTopic($validated);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:topics,slug,'.$topic->id,
            'short_description' => 'nullable|string|max:500',
        ]);
        
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_members_only'] = $request->has('is_members_only');

        $this->topicService->updateTopic($topic, $validated);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        try {
            $this->topicService->deleteTopic($topic);
            return redirect()->route('admin.topics.index')
                ->with('success', 'Topic deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.topics.index')
                ->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Topic $topic)
    {
        $topic->update(['is_active' => !$topic->is_active]);
        
        return redirect()->back()
            ->with('success', 'Topic visibility toggled successfully.');
    }
}
