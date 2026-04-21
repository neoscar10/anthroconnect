<?php

namespace App\Livewire\Admin\Lms\Resources;

use App\Models\Lms\LmsResource;
use App\Models\Lms\LmsModule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $moduleFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'moduleFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteResource($id)
    {
        $res = LmsResource::findOrFail($id);
        $res->delete();
        session()->flash('success', 'Resource removed from archives.');
    }

    public function render()
    {
        $query = LmsResource::with('module');

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->moduleFilter) {
            $query->where('lms_module_id', $this->moduleFilter);
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate(15);
        $modules = LmsModule::orderBy('title')->get();

        return view('livewire.admin.lms.resources.index', [
            'resources' => $resources,
            'modules' => $modules,
        ])->layout('layouts.admin', ['title' => 'Global Resources Archive']);
    }
}
