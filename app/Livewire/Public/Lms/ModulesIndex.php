<?php

namespace App\Livewire\Public\Lms;

use App\Services\Lms\LmsPublicService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class ModulesIndex extends Component
{
    use WithPagination;

    #[Url(as: 'topic_id', except: '')]
    public $topicId = '';

    #[Url(as: 'level', except: '')]
    public $level = '';

    #[Url(as: 'search', except: '')]
    public $search = '';

    public function updating($name)
    {
        if (in_array($name, ['topicId', 'level', 'search'])) {
            $this->resetPage();
        }
    }

    public function setTopic($id)
    {
        $this->topicId = $id;
        $this->resetPage();
    }

    public function render(LmsPublicService $lmsService)
    {
        $filters = [
            'topic_id' => $this->topicId,
            'level' => $this->level,
            'search' => $this->search,
        ];

        $topics = $lmsService->getActiveModuleTopics();
        $modules = $lmsService->getPublishedModules($filters);

        return view('livewire.public.lms.modules-index', [
            'topics' => $topics,
            'modules' => $modules,
        ])->title('Anthropology Modules');
    }
}
