<?php

namespace App\Livewire\Admin\Tags;

use App\Models\Tag;
use App\Models\TagGroup;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class TagsIndex extends Component
{
    use WithPagination;

    public $selectedGroupId = null;
    public $groupSearch = '';
    public $tagSearch = '';

    // Group Form
    public $groupId;
    public $groupName = '';
    public $groupDescription = '';
    public $groupSelectionType = 'multi_select';
    public $groupIsActive = true;
    public $isGroupModalOpen = false;

    // Tag Form
    public $tagId;
    public $tagName = '';
    public $tagDescription = '';
    public $tagIsActive = true;
    public $isTagModalOpen = false;

    public function mount()
    {
        $firstGroup = TagGroup::orderBy('display_order')->first();
        if ($firstGroup) {
            $this->selectedGroupId = $firstGroup->id;
        }
    }

    public function selectGroup($id)
    {
        $this->selectedGroupId = $id;
        $this->resetPage();
    }

    // Group Management
    public function openGroupModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['groupName', 'groupDescription', 'groupSelectionType', 'groupIsActive', 'groupId']);

        if ($id) {
            $group = TagGroup::findOrFail($id);
            $this->groupId = $group->id;
            $this->groupName = $group->name;
            $this->groupDescription = $group->description;
            $this->groupSelectionType = $group->selection_type;
            $this->groupIsActive = $group->is_active;
        }

        $this->isGroupModalOpen = true;
    }

    public function saveGroup()
    {
        $this->validate([
            'groupName' => 'required|string|max:255',
            'groupSelectionType' => 'required|in:single_select,multi_select',
        ]);

        $data = [
            'name' => $this->groupName,
            'slug' => Str::slug($this->groupName),
            'description' => $this->groupDescription,
            'selection_type' => $this->groupSelectionType,
            'is_active' => $this->groupIsActive,
        ];

        if ($this->groupId) {
            TagGroup::findOrFail($this->groupId)->update($data);
            session()->flash('success', 'Tag Group updated successfully.');
        } else {
            $group = TagGroup::create($data);
            $this->selectedGroupId = $group->id;
            session()->flash('success', 'Tag Group created successfully.');
        }

        $this->isGroupModalOpen = false;
    }

    // Tag Management
    public function openTagModal($id = null)
    {
        if (!$this->selectedGroupId) return;

        $this->resetValidation();
        $this->reset(['tagName', 'tagDescription', 'tagIsActive', 'tagId']);

        if ($id) {
            $tag = Tag::findOrFail($id);
            $this->tagId = $tag->id;
            $this->tagName = $tag->name;
            $this->tagDescription = $tag->description;
            $this->tagIsActive = $tag->is_active;
        }

        $this->isTagModalOpen = true;
    }

    public function saveTag()
    {
        $this->validate([
            'tagName' => 'required|string|max:255',
        ]);

        $data = [
            'tag_group_id' => $this->selectedGroupId,
            'name' => $this->tagName,
            'slug' => Str::slug($this->tagName),
            'description' => $this->tagDescription,
            'is_active' => $this->tagIsActive,
        ];

        if ($this->tagId) {
            Tag::findOrFail($this->tagId)->update($data);
            session()->flash('success', 'Tag updated successfully.');
        } else {
            Tag::create($data);
            session()->flash('success', 'Tag created successfully.');
        }

        $this->isTagModalOpen = false;
    }

    public function deleteTag($id)
    {
        Tag::findOrFail($id)->delete();
        session()->flash('success', 'Tag deleted.');
    }

    public function deleteGroup($id)
    {
        $group = TagGroup::findOrFail($id);
        if ($group->tags()->count() > 0) {
            session()->flash('error', 'Cannot delete group with tags. Delete tags first.');
            return;
        }
        $group->delete();
        $this->selectedGroupId = TagGroup::first()?->id;
        session()->flash('success', 'Tag Group deleted.');
    }

    public function render()
    {
        $groups = TagGroup::query()
            ->withCount('tags')
            ->when($this->groupSearch, fn($q) => $q->where('name', 'like', "%{$this->groupSearch}%"))
            ->orderBy('display_order')
            ->get();

        $selectedGroup = $this->selectedGroupId ? TagGroup::find($this->selectedGroupId) : null;
        
        $tags = $selectedGroup 
            ? $selectedGroup->tags()
                ->when($this->tagSearch, fn($q) => $q->where('name', 'like', "%{$this->tagSearch}%"))
                ->paginate(20)
            : collect([]);

        return view('livewire.admin.tags.tags-index', [
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'tags' => $tags
        ])->layout('layouts.admin', ['title' => 'Tags Management']);
    }
}
