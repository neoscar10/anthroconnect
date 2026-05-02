<?php

namespace App\Livewire\Admin\Lms\ModuleClasses;

use App\Models\Exam\ExamQuestion;
use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsModuleClass;
use App\Models\Lms\LmsClassAssessment;
use App\Services\Lms\ClassMcqService;
use Livewire\Component;

class ClassMcqManager extends Component
{
    public LmsModule $module;
    public LmsModuleClass $class;
    public ?LmsClassAssessment $assessment = null;

    // Filters
    public $search = '';
    public $statusFilter = 'all';

    // Modal State
    public $isModalOpen = false;
    public $editingQuestionId = null;

    // Form Fields
    public $question_text = '';
    public $explanation = '';
    public $marks = 1;
    public $options = [];
    public $correct_option_index = 0;

    // Assessment Configuration Fields
    public $assessment_title = '';
    public $assessment_description = '';
    public $assessment_instructions = '';
    public $duration_minutes = 0;
    public $passing_marks = 0;
    public $allow_retake = true;
    public $show_results_immediately = true;
    public $show_correct_answers = true;
    public $randomize_questions = false;
    public $randomize_options = false;
    public $is_assessment_published = false;

    // View State
    public $isConfigModalOpen = false;
    public $view = 'questions'; // questions, results

    protected $rules = [
        'question_text' => 'required|string|min:5',
        'explanation' => 'nullable|string',
        'marks' => 'required|integer|min:1',
        'options' => 'required|array|min:2',
        'options.*.text' => 'required|string',
    ];

    public function mount(LmsModule $module, LmsModuleClass $class)
    {
        $this->module = $module;
        $this->class = $class;
        $this->loadAssessment();
        $this->resetForm();
    }

    public function loadAssessment()
    {
        $this->assessment = LmsClassAssessment::where('lms_module_class_id', $this->class->id)->first();
        
        if ($this->assessment) {
            $this->assessment_title = $this->assessment->title;
            $this->assessment_description = $this->assessment->description;
            $this->assessment_instructions = $this->assessment->instructions;
            $this->duration_minutes = $this->assessment->duration_minutes;
            $this->passing_marks = $this->assessment->passing_marks;
            $this->allow_retake = $this->assessment->allow_retake;
            $this->show_results_immediately = $this->assessment->show_results_immediately;
            $this->show_correct_answers = $this->assessment->show_correct_answers;
            $this->randomize_questions = $this->assessment->randomize_questions;
            $this->randomize_options = $this->assessment->randomize_options;
            $this->is_assessment_published = $this->assessment->is_published;
        } else {
            $this->assessment_title = 'Assessment: ' . $this->class->title;
        }
    }

    public function resetForm()
    {
        $this->reset([
            'question_text', 'explanation', 'marks', 
            'editingQuestionId', 'isModalOpen'
        ]);
        $this->marks = 1;
        $this->is_published = true;
        $this->options = [
            ['text' => ''],
            ['text' => ''],
            ['text' => ''],
            ['text' => ''],
        ];
        $this->correct_option_index = 0;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function openConfigModal()
    {
        $this->loadAssessment();
        $this->isConfigModalOpen = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $question = ExamQuestion::with('options')->findOrFail($id);
        
        $this->editingQuestionId = $question->id;
        $this->question_text = $question->question_text;
        $this->explanation = $question->explanation;
        $this->marks = $question->marks;
        
        $this->options = $question->options->map(fn($o) => ['text' => $o->option_text])->toArray();
        $this->correct_option_index = $question->options->search(fn($o) => $o->is_correct);
        
        $this->isModalOpen = true;
    }

    public function addOption()
    {
        $this->options[] = ['text' => ''];
    }

    public function removeOption($index)
    {
        if (count($this->options) > 2) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
            if ($this->correct_option_index >= count($this->options)) {
                $this->correct_option_index = 0;
            }
        }
    }

    public function setCorrectOption($index)
    {
        $this->correct_option_index = $index;
    }

    public function save(ClassMcqService $service)
    {
        if (!$this->assessment) {
            $this->saveAssessment();
        }

        $this->validate();

        $data = [
            'question_text' => $this->question_text,
            'explanation' => $this->explanation,
            'marks' => $this->marks,
            'status' => 'published',
            'options' => $this->options,
            'correct_option_index' => $this->correct_option_index,
        ];

        if ($this->editingQuestionId) {
            $question = ExamQuestion::findOrFail($this->editingQuestionId);
            $service->updateLmsClassQuestion($question, $data, auth()->user());
        } else {
            $service->createForLmsClass($this->assessment, $data, auth()->user());
        }

        $this->isModalOpen = false;
        $this->resetForm();
        $this->dispatch('mcq-saved');
    }

    public function saveAssessment()
    {
        $data = [
            'lms_module_id' => $this->module->id,
            'lms_module_class_id' => $this->class->id,
            'title' => $this->assessment_title,
            'description' => $this->assessment_description,
            'instructions' => $this->assessment_instructions,
            'duration_minutes' => $this->duration_minutes,
            'passing_marks' => $this->passing_marks,
            'allow_retake' => $this->allow_retake,
            'show_results_immediately' => $this->show_results_immediately,
            'show_correct_answers' => $this->show_correct_answers,
            'randomize_questions' => $this->randomize_questions,
            'randomize_options' => $this->randomize_options,
            'is_published' => $this->is_assessment_published,
        ];

        if ($this->assessment) {
            $this->assessment->update($data);
        } else {
            $this->assessment = LmsClassAssessment::create($data);
            
            // Link existing questions that belong to this class but have no assessment_id
            ExamQuestion::where('lms_module_class_id', $this->class->id)
                ->whereNull('lms_class_assessment_id')
                ->update(['lms_class_assessment_id' => $this->assessment->id]);
        }

        $this->isConfigModalOpen = false;
        $this->dispatch('assessment-updated');
    }

    public function toggleAssessmentPublish()
    {
        if (!$this->assessment) {
            $this->saveAssessment();
        }
        
        $this->is_assessment_published = !$this->is_assessment_published;
        $this->assessment->update(['is_published' => $this->is_assessment_published]);
    }

    public function duplicate($id, ClassMcqService $service)
    {
        $question = ExamQuestion::findOrFail($id);
        $service->duplicateQuestion($question, auth()->user());
    }

    public function delete($id, ClassMcqService $service)
    {
        $question = ExamQuestion::findOrFail($id);
        $service->deleteLmsClassQuestion($question);
    }

    public function updateOrder($orderedIds, ClassMcqService $service)
    {
        if ($this->assessment) {
            $service->reorderQuestions($this->assessment, $orderedIds);
        }
    }

    public function deleteAttempt($id)
    {
        LmsClassAssessmentAttempt::findOrFail($id)->delete();
        $this->dispatch('attempt-deleted');
    }

    public function exportCsv()
    {
        if (!$this->assessment) return;

        $attempts = $this->assessment->attempts()
            ->with('user')
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=assessment_results.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($attempts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student Name', 'Email', 'Date Attempted', 'Score', 'Total Marks', 'Percentage', 'Status', 'Summary']);

            foreach ($attempts as $attempt) {
                fputcsv($file, [
                    $attempt->user->name,
                    $attempt->user->email,
                    $attempt->submitted_at->format('Y-m-d H:i:s'),
                    $attempt->score,
                    $attempt->total_marks,
                    $attempt->percentage . '%',
                    $attempt->passed ? 'Passed' : 'Failed',
                    $attempt->summary
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, "results-{$this->assessment->id}.csv", $headers);
    }

    public function render()
    {
        $questions = collect();
        if ($this->assessment) {
            $questions = $this->assessment->questions()
                ->with('options')
                ->when($this->search, fn($q) => $q->where('question_text', 'like', '%' . $this->search . '%'))
                ->get();
        }

        $stats = [
            'total' => $questions->count(),
            'total_marks' => $questions->sum('marks'),
        ];

        $attempts = collect();
        if ($this->assessment && $this->view === 'results') {
            $attempts = $this->assessment->attempts()
                ->with('user')
                ->whereNotNull('submitted_at')
                ->orderByDesc('submitted_at')
                ->get();
        }

        return view('livewire.admin.lms.module-classes.class-mcq-manager', [
            'questions' => $questions,
            'attempts' => $attempts,
            'stats' => $stats
        ]);
    }
}
