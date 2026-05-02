<?php

namespace App\Livewire\Admin\Lms\Modules;

use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsLesson;
use App\Models\Lms\LmsResource;
use App\Models\Topic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Editor extends Component
{
    use WithFileUploads;

    public $module;
    public $isEdit = false;

    // Module fields
    public $title = '';
    public $slug = '';
    public $short_description = '';
    public $overview = '';
    public $level = 'beginner';
    
    // Class Navigation State
    public $selectedClassId = null;
    
    // Class Modal State
    public $isClassModalOpen = false;
    public $editingClassId;
    public $class_title = '';
    public $class_description = '';
    public $class_is_published = true;
    public $class_sort_order = 0;

    // Lesson Modal State
    public $isLessonModalOpen = false;
    public $editingLessonId;
    public $lesson_title = '';
    public $lesson_slug = '';
    public $lesson_short_description = '';
    public $lesson_video_source_type = 'url';
    public $lesson_video_path;
    public $lesson_video_url = '';
    public $lesson_duration_minutes = '0';
    public $lesson_sort_order = 0;
    public $lesson_is_published = true;
    public $lesson_is_members_only = false;
    public $lesson_notes = '';

    // Resource Modal State
    public $isResourceModalOpen = false;
    public $editingResourceId;
    public $resource_title = '';
    public $resource_short_description = '';
    public $resource_file_path;
    public $resource_sort_order = 0;
    public $resource_is_published = true;
    public $resource_is_members_only = false;

    public function mount(LmsModule $lmsModule)
    {
        if (!$lmsModule->exists) {
            return redirect()->route('admin.lms.modules.index');
        }

        $this->module = $lmsModule->load(['classes' => function($q) {
            $q->withCount(['lessons', 'resources', 'mcqQuestions'])->orderBy('sort_order');
        }]);
        $this->isEdit = true;
        $this->title = $lmsModule->title;
        $this->slug = $lmsModule->slug;
        $this->short_description = $lmsModule->short_description;
        $this->overview = $lmsModule->overview;
        $this->level = $lmsModule->level;
    }

    public function updatedTitle()
    {
        // Slug generation is primarily handled in the creation modal on the index.
        // We only regenerate here if it's somehow empty.
        if (!$this->slug) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function saveModule()
    {
        session()->flash('success', 'Module structure saved.');
    }

    // --- Class Management ---

    public function openClassModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['editingClassId', 'class_title', 'class_description', 'class_is_published', 'class_sort_order']);
        
        if ($id) {
            $class = \App\Models\Lms\LmsModuleClass::findOrFail($id);
            $this->editingClassId = $class->id;
            $this->class_title = $class->title;
            $this->class_description = $class->description;
            $this->class_is_published = $class->is_published;
            $this->class_sort_order = $class->sort_order;
        }

        $this->isClassModalOpen = true;
    }

    public function saveClass()
    {
        $this->validate([
            'class_title' => 'required|string|max:120',
            'class_description' => 'nullable|string|max:1000',
        ]);

        $data = [
            'lms_module_id' => $this->module->id,
            'title' => $this->class_title,
            'description' => $this->class_description,
            'is_published' => $this->class_is_published,
        ];

        if ($this->editingClassId) {
            \App\Models\Lms\LmsModuleClass::find($this->editingClassId)->update($data);
        } else {
            $data['sort_order'] = \App\Models\Lms\LmsModuleClass::where('lms_module_id', $this->module->id)->max('sort_order') + 1;
            \App\Models\Lms\LmsModuleClass::create($data);
        }

        $this->isClassModalOpen = false;
        $this->module->load(['classes' => function($q) {
            $q->withCount(['lessons', 'resources', 'mcqQuestions'])->orderBy('sort_order');
        }]);
    }

    public function openClass($id)
    {
        $this->selectedClassId = $id;
        $this->module->load(['lessons', 'resources']);
    }

    public function closeClass()
    {
        $this->selectedClassId = null;
    }

    public function deleteClass($id)
    {
        $class = \App\Models\Lms\LmsModuleClass::findOrFail($id);
        $class->delete();
        $this->module->load(['classes' => function($q) {
            $q->withCount(['lessons', 'resources', 'mcqQuestions'])->orderBy('sort_order');
        }]);
    }

    public function updateClassOrder($items)
    {
        foreach ($items as $item) {
            \App\Models\Lms\LmsModuleClass::where('id', $item['value'])->update(['sort_order' => $item['order']]);
        }
        $this->module->load(['classes' => function($q) {
            $q->withCount(['lessons', 'resources'])->orderBy('sort_order');
        }]);
    }

    public function moveLegacy()
    {
        $service = new \App\Services\Lms\ModuleClassService();
        $service->moveLegacyContentToClass($this->module, 'Introduction Class');
        $this->module->load(['lessons', 'resources', 'classes' => function($q) {
            $q->withCount(['lessons', 'resources'])->orderBy('sort_order');
        }]);
        session()->flash('success', 'Legacy content moved to Introduction Class.');
    }

    // --- Lesson Management ---

    public function openLessonModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['editingLessonId', 'lesson_title', 'lesson_slug', 'lesson_short_description', 'lesson_video_source_type', 'lesson_video_path', 'lesson_video_url', 'lesson_duration_minutes', 'lesson_sort_order', 'lesson_is_published', 'lesson_is_members_only', 'lesson_notes']);
        
        if ($id) {
            $lesson = LmsLesson::findOrFail($id);
            $this->editingLessonId = $lesson->id;
            $this->lesson_title = $lesson->title;
            $this->lesson_slug = $lesson->slug;
            $this->lesson_short_description = $lesson->short_description;
            $this->lesson_video_source_type = $lesson->video_source_type;
            $this->lesson_video_url = $lesson->video_url;
            $this->lesson_duration_minutes = $lesson->duration_minutes;
            $this->lesson_sort_order = $lesson->sort_order;
            $this->lesson_is_preview = $lesson->is_preview;
            $this->lesson_is_published = $lesson->is_published;
            $this->lesson_is_members_only = $lesson->is_members_only;
            $this->lesson_notes = $lesson->notes;
        }

        $this->isLessonModalOpen = true;
    }

    public function saveLesson()
    {
        if (!$this->module) return;

        $this->validate([
            'lesson_title' => 'required|string|max:255',
            'lesson_video_source_type' => 'required|in:upload,url',
            'lesson_video_path' => ($this->editingLessonId ? 'nullable' : 'required_if:lesson_video_source_type,upload') . '|file|max:512000', // 500MB
            'lesson_video_url' => 'nullable|url|required_if:lesson_video_source_type,url',
            'lesson_is_members_only' => 'boolean',
        ]);

        $data = [
            'lms_module_id' => $this->module->id,
            'lms_module_class_id' => $this->selectedClassId,
            'title' => $this->lesson_title,
            'slug' => Str::slug($this->lesson_title),
            'short_description' => $this->lesson_short_description,
            'video_source_type' => $this->lesson_video_source_type,
            'video_url' => $this->lesson_video_url,
            'duration_minutes' => $this->lesson_duration_minutes,
            'sort_order' => $this->lesson_sort_order,
            'is_published' => $this->lesson_is_published,
            'is_members_only' => $this->lesson_is_members_only,
            'notes' => $this->lesson_notes,
            'updated_by' => auth()->id(),
        ];

        if ($this->lesson_video_path) {
            $data['video_path'] = $this->lesson_video_path->store('lms/lessons/videos', 'public');
        }

        if (!$this->editingLessonId) {
            $data['sort_order'] = LmsLesson::where('lms_module_id', $this->module->id)
                ->where('lms_module_class_id', $this->selectedClassId)
                ->max('sort_order') + 1;
            $data['created_by'] = auth()->id();
            LmsLesson::create($data);
        } else {
            LmsLesson::find($this->editingLessonId)->update($data);
        }

        $this->isLessonModalOpen = false;
        $this->module->load('lessons');
        session()->flash('success', 'Lesson saved successfully.');
    }

    public function updatedLessonVideoPath()
    {
        if ($this->lesson_video_path && $this->lesson_video_source_type === 'upload') {
            $this->lesson_duration_minutes = $this->getVideoDuration($this->lesson_video_path->getRealPath());
        }
    }

    private function getVideoDuration($path)
    {
        try {
            $getID3 = new \getID3;
            $fileInfo = $getID3->analyze($path);
            
            if (isset($fileInfo['playtime_seconds'])) {
                return ceil($fileInfo['playtime_seconds'] / 60);
            }
        } catch (\Exception $e) {
            // Log or handle error if needed
        }
        
        return 0;
    }

    public function deleteLesson($id)
    {
        LmsLesson::findOrFail($id)->delete();
        $this->module->load('lessons');
    }

    public function updateLessonOrder($items)
    {
        foreach ($items as $item) {
            LmsLesson::where('id', $item['value'])->update(['sort_order' => $item['order']]);
        }
        $this->module->load('lessons');
    }

    // --- Resource Management ---

    public function openResourceModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['editingResourceId', 'resource_title', 'resource_short_description', 'resource_file_path', 'resource_sort_order', 'resource_is_published', 'resource_is_members_only']);

        if ($id) {
            $resource = LmsResource::findOrFail($id);
            $this->editingResourceId = $resource->id;
            $this->resource_title = $resource->title;
            $this->resource_short_description = $resource->short_description;
            $this->resource_sort_order = $resource->sort_order;
            $this->resource_is_published = $resource->is_published;
            $this->resource_is_members_only = $resource->is_members_only;
        }

        $this->isResourceModalOpen = true;
    }

    public function saveResource()
    {
        if (!$this->module) return;

        $this->validate([
            'resource_title' => 'required|string|max:255',
            'resource_file_path' => 'nullable|mimes:pdf|max:10240', // 10MB
            'resource_is_members_only' => 'boolean',
        ]);

        $data = [
            'lms_module_id' => $this->module->id,
            'lms_module_class_id' => $this->selectedClassId,
            'title' => $this->resource_title,
            'short_description' => $this->resource_short_description,
            'sort_order' => $this->resource_sort_order,
            'is_published' => $this->resource_is_published,
            'is_members_only' => $this->resource_is_members_only,
            'updated_by' => auth()->id(),
        ];

        if ($this->resource_file_path) {
            $data['file_path'] = $this->resource_file_path->store('lms/resources', 'public');
        } elseif (!$this->editingResourceId) {
            $this->addError('resource_file_path', 'PDF file is required.');
            return;
        }

        if ($this->editingResourceId) {
            LmsResource::find($this->editingResourceId)->update($data);
        } else {
            $data['created_by'] = auth()->id();
            LmsResource::create($data);
        }

        $this->isResourceModalOpen = false;
        $this->module->load('resources');
        session()->flash('success', 'Resource saved successfully.');
    }

    public function deleteResource($id)
    {
        LmsResource::findOrFail($id)->delete();
        $this->module->load('resources');
    }

    public function render()
    {
        return view('livewire.admin.lms.modules.editor')
            ->layout('layouts.admin', ['title' => $this->isEdit ? 'Edit Module' : 'Create Module']);
    }
}
