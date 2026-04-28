<?php

namespace App\Livewire\Admin\Exams\Questions;

use App\Models\Exam\ExamQuestion;
use App\Models\TagGroup;
use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsLesson;
use App\Models\Lms\LmsResource;
use App\Models\LibraryResource;
use App\Services\Exam\ExamQuestionService;
use Livewire\Component;
use Livewire\WithPagination;

class ExamQuestionIndex extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $status = '';
    public $exam_type = 'UPSC';
    public $year = '';
    public $tagFilters = [];

    // Modal State
    public $isModalOpen = false;
    public $activeTab = 'question'; // question, tags, guidelines, model, rubric, publishing
    public $editingId = null;

    // Form Fields
    public $title = '';
    public $question_text = '';
    public $short_context = '';
    public $form_exam_type = 'UPSC';
    public $form_year = '';
    public $marks = 10;
    public $word_limit = 150;
    public $selectedTags = [];
    public $answer_guidelines = '';
    public $model_answer = '';
    public $evaluation_rubric = []; // [{criteria: '', marks: ''}]
    public $learning_resources = []; // [{title: '', type: '', url: ''}]
    public $form_status = 'published';
    public $access_type = 'public';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'year' => ['except' => ''],
        'tagFilters' => ['except' => []],
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'status', 'year']) || str_starts_with($propertyName, 'tagFilters')) {
            $this->resetPage();
        }

        // Handle cascading reset for resources
        if (str_starts_with($propertyName, 'learning_resources.') && str_ends_with($propertyName, '.module_id')) {
            $index = explode('.', $propertyName)[1];
            $this->learning_resources[$index]['id'] = '';
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('set-tags', id: 'exam-tag-selector', tags: []);
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $question = ExamQuestion::with('tags')->findOrFail($id);
        $this->editingId = $question->id;
        $this->title = $question->title;
        $this->question_text = $question->question_text;
        $this->short_context = $question->short_context;
        $this->form_exam_type = $question->exam_type;
        $this->form_paper = $question->paper;
        $this->form_section = $question->section;
        $this->form_year = $question->year;
        $this->marks = $question->marks;
        $this->word_limit = $question->word_limit;
        $this->suggested_time_minutes = $question->suggested_time_minutes;
        $this->form_difficulty = $question->difficulty;
        $this->selectedTags = $question->tags->pluck('id')->toArray();
        $this->answer_guidelines = $question->answer_guidelines;
        $this->model_answer = $question->model_answer;
        $this->evaluation_rubric = $question->evaluation_rubric ?? [];
        $this->learning_resources = $question->learning_resources ?? [];
        $this->form_status = $question->status;
        $this->access_type = $question->access_type ?? 'public';

        $this->isModalOpen = true;
        $this->dispatch('set-tags', id: 'exam-tag-selector', tags: $this->selectedTags);
    }

    public function resetForm()
    {
        $this->reset([
            'editingId', 'title', 'question_text', 'short_context', 'form_exam_type',
            'form_year', 'marks', 'word_limit', 'selectedTags', 'answer_guidelines', 'model_answer',
            'evaluation_rubric', 'learning_resources', 'form_status', 'access_type',
            'activeTab'
        ]);
        $this->form_exam_type = 'UPSC';
        $this->form_status = 'published';
        $this->access_type = 'public';
        $this->activeTab = 'question';
        $this->evaluation_rubric = [];
        $this->learning_resources = [];
    }

    public function nextStep()
    {
        $steps = ['question', 'tags', 'guidelines', 'model', 'rubric', 'publishing'];
        $currentIndex = array_search($this->activeTab, $steps);
        
        if ($currentIndex !== false && $currentIndex < count($steps) - 1) {
            $this->activeTab = $steps[$currentIndex + 1];
        }
    }

    public function prevStep()
    {
        $steps = ['question', 'tags', 'guidelines', 'model', 'rubric', 'publishing'];
        $currentIndex = array_search($this->activeTab, $steps);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            $this->activeTab = $steps[$currentIndex - 1];
        }
    }

    public function addRubricRow()
    {
        $this->evaluation_rubric[] = ['criteria' => '', 'marks' => ''];
    }

    public function removeRubricRow($index)
    {
        unset($this->evaluation_rubric[$index]);
        $this->evaluation_rubric = array_values($this->evaluation_rubric);
    }

    public function addResourceRow()
    {
        $this->learning_resources[] = ['title' => '', 'type' => 'Course Module', 'module_id' => '', 'id' => '', 'url' => '', 'description' => ''];
    }

    public function removeResourceRow($index)
    {
        unset($this->learning_resources[$index]);
        $this->learning_resources = array_values($this->learning_resources);
    }

    public function save(ExamQuestionService $service)
    {
        // Process resources to ensure titles and URLs are captured from database records
        $processedResources = [];
        foreach ($this->learning_resources as $res) {
            $processed = $res;
            if (!empty($res['id'])) {
                if ($res['type'] === 'Course Module') {
                    $module = LmsModule::find($res['id']);
                    $processed['title'] = $module?->title;
                    $processed['url'] = route('modules.show', $module?->slug ?? '');
                } elseif ($res['type'] === 'Lesson Video') {
                    $lesson = LmsLesson::find($res['id']);
                    $processed['title'] = $lesson?->title;
                    $processed['url'] = route('lessons.show', ['moduleSlug' => $lesson?->module?->slug ?? '', 'lessonSlug' => $lesson?->slug ?? '']);
                } elseif ($res['type'] === 'Library Resource') {
                    $libRes = LibraryResource::find($res['id']);
                    $processed['title'] = $libRes?->title;
                    $processed['url'] = route('library.show', $libRes?->slug ?? '');
                } elseif ($res['type'] === 'Module Resource (PDF)') {
                    $lmsRes = LmsResource::find($res['id']);
                    $processed['title'] = $lmsRes?->title;
                    $processed['url'] = $lmsRes?->file_path ? \Illuminate\Support\Facades\Storage::url($lmsRes->file_path) : '#';
                }
            }
            $processedResources[] = $processed;
        }

        $data = [
            'title' => $this->title,
            'question_text' => $this->question_text,
            'short_context' => $this->short_context,
            'exam_type' => 'UPSC',
            'year' => $this->form_year,
            'marks' => $this->marks,
            'word_limit' => $this->word_limit,
            'tag_ids' => $this->selectedTags,
            'answer_guidelines' => $this->answer_guidelines,
            'model_answer' => $this->model_answer,
            'evaluation_rubric' => $this->evaluation_rubric,
            'learning_resources' => $processedResources,
            'status' => $this->form_status,
            'access_type' => $this->access_type,
        ];

        try {
            if ($this->editingId) {
                $question = ExamQuestion::findOrFail($this->editingId);
                $service->update($question, $data, auth()->user());
                session()->flash('success', 'Question updated successfully.');
            } else {
                $service->create($data, auth()->user());
                session()->flash('success', 'Question created successfully.');
            }

            $this->isModalOpen = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->addError('save_error', $e->getMessage());
        }
    }

    public function delete($id, ExamQuestionService $service)
    {
        $question = ExamQuestion::findOrFail($id);
        $service->delete($question);
        session()->flash('success', 'Question deleted successfully.');
    }

    public function toggleStatus($id, ExamQuestionService $service)
    {
        $question = ExamQuestion::findOrFail($id);
        if ($question->status === 'published') {
            $service->archive($question, auth()->user());
        } else {
            $service->publish($question, auth()->user());
        }
    }

    public function render()
    {
        $service = app(ExamQuestionService::class);
        $questions = $service->paginate([
            'search' => $this->search,
            'status' => $this->status,
            'exam_type' => 'UPSC',
            'year' => $this->year,
            'tag_ids' => $this->tagFilters,
        ]);

        $stats = [
            'total' => ExamQuestion::count(),
            'published' => ExamQuestion::published()->count(),
            'drafts' => ExamQuestion::draft()->count(),
        ];

        $filterableTagGroups = TagGroup::getGroupsWithUsage(ExamQuestion::class);
        $allTagGroups = TagGroup::active()->with('activeTags')->get();
        
        $allModules = LmsModule::published()->orderBy('title')->get();
        $allLessons = LmsLesson::published()->whereNotNull('video_url')->orWhereNotNull('video_path')->orderBy('title')->get();
        $allLmsResources = LmsResource::where('is_published', true)->orderBy('title')->get();
        $allLibraryResources = LibraryResource::orderBy('title')->get();

        return view('livewire.admin.exams.questions.exam-question-index', compact(
            'questions', 'stats', 'filterableTagGroups', 'allTagGroups', 
            'allModules', 'allLessons', 'allLmsResources', 'allLibraryResources'
        ))
            ->layout('layouts.admin', ['title' => 'Exams: Question Management']);
    }
}
