<?php

namespace App\Livewire\Frontend\Exams;

use App\Services\Exam\ExamQuestionFrontendService;
use App\Models\TagGroup;
use App\Models\Exam\ExamQuestion;
use Livewire\Component;
use Livewire\WithPagination;

class ExamQuestionListPage extends Component
{
    use WithPagination;

    public $search = '';
    public $year = '';
    public $kind = '';
    public $selectedTags = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'year' => ['except' => ''],
        'kind' => ['except' => ''],
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'year']) || str_starts_with($propertyName, 'selectedTags')) {
            $this->resetPage();
        }
    }

    public function render(ExamQuestionFrontendService $service)
    {
        $questions = $service->paginatePublished([
            'search' => $this->search,
            'year' => $this->year,
            'kind' => $this->kind,
            'tags' => $this->selectedTags,
        ]);

        $qotd = $service->questionOfTheDay();

        $tagGroups = TagGroup::getGroupsWithUsage(ExamQuestion::class);
        
        $availableYears = ExamQuestion::published()
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('livewire.frontend.exams.exam-question-list-page', [
            'questions' => $questions,
            'qotd' => $qotd,
            'tagGroups' => $tagGroups,
            'availableYears' => $availableYears,
        ])->layout('layouts.public', ['title' => 'AnthroConnect | Practice Exams']);
    }
}
