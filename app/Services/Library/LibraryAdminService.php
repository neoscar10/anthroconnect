<?php

namespace App\Services\Library;

use App\Models\LibraryResource;
use App\Models\LibraryResourceType;
use App\Models\LibraryDiscipline;
use App\Models\LibraryRegion;
use App\Models\LibraryTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryAdminService
{
    /**
     * List resources with filters for admin.
     */
    public function listResourcesForAdmin(array $filters = [])
    {
        $query = LibraryResource::query()->with(['resourceType', 'region']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('author_display', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type_id'])) {
            $query->where('resource_type_id', $filters['type_id']);
        }



        return $query->latest();
    }

    /**
     * Create a new library resource.
     */
    public function createResource(array $data)
    {
        return DB::transaction(function () use ($data) {
            $resource = LibraryResource::create($data);

            if (isset($data['topics'])) {
                $resource->topics()->sync($data['topics']);
            }

            if (isset($data['tags'])) {
                $this->syncTags($resource, $data['tags']);
            }

            if (isset($data['related_resources'])) {
                $this->syncRelatedResources($resource, $data['related_resources']);
            }

            if (isset($data['related_learning'])) {
                $this->syncRelatedLearning($resource, $data['related_learning']);
            }

            return $resource;
        });
    }

    /**
     * Update an existing library resource.
     */
    public function updateResource(LibraryResource $resource, array $data)
    {
        return DB::transaction(function () use ($resource, $data) {
            $resource->update($data);

            if (isset($data['topics'])) {
                $resource->topics()->sync($data['topics']);
            }

            if (isset($data['tags'])) {
                $this->syncTags($resource, $data['tags']);
            }

            if (isset($data['related_resources'])) {
                $this->syncRelatedResources($resource, $data['related_resources']);
            }

            if (isset($data['related_learning'])) {
                $this->syncRelatedLearning($resource, $data['related_learning']);
            }

            return $resource;
        });
    }

    /**
     * Handle file uploads for a resource.
     */
    public function handleFileUploads(LibraryResource $resource, $files)
    {
        if (isset($files['cover_image'])) {
            if ($resource->cover_image_path) {
                Storage::delete($resource->cover_image_path);
            }
            $resource->cover_image_path = $files['cover_image']->store('library/covers', 'public');
        }

        if (isset($files['resource_file'])) {
            if ($resource->file_path) {
                Storage::delete($resource->file_path);
            }
            $resource->file_path = $files['resource_file']->store('library/resources', 'public');
        }

        if (isset($files['preview_file'])) {
            if ($resource->preview_file_path) {
                Storage::delete($resource->preview_file_path);
            }
            $resource->preview_file_path = $files['preview_file']->store('library/previews', 'public');
        }

        $resource->save();
    }

    /**
     * Sync related library resources.
     */
    protected function syncRelatedResources(LibraryResource $resource, array $relatedData)
    {
        $syncData = [];
        foreach ($relatedData as $item) {
            if (isset($item['id'])) {
                $syncData[$item['id']] = [
                    'relation_type' => $item['relation_type'] ?? 'more_resource',
                    'sort_order' => $item['sort_order'] ?? 0,
                ];
            }
        }
        $resource->relatedResources()->sync($syncData);
    }

    /**
     * Sync related learning items (polymorphic).
     */
    protected function syncRelatedLearning(LibraryResource $resource, array $learningData)
    {
        $resource->relatedLearningItems()->delete();

        foreach ($learningData as $item) {
            if (isset($item['linkable_id']) && isset($item['linkable_type'])) {
                $resource->relatedLearningItems()->create([
                    'linkable_id' => $item['linkable_id'],
                    'linkable_type' => $item['linkable_type'],
                    'label' => $item['label'] ?? null,
                    'relation_type' => $item['relation_type'] ?? 'module',
                    'sort_order' => $item['sort_order'] ?? 0,
                ]);
            }
        }
    }

    /**
     * Sync tags from a comma-separated string.
     */
    protected function syncTags(LibraryResource $resource, $tagsString)
    {
        if (is_array($tagsString)) {
            $tags = $tagsString;
        } else {
            $tags = array_map('trim', explode(',', $tagsString));
        }
        
        $tagIds = [];
        foreach ($tags as $tagName) {
            if (empty($tagName)) continue;
            
            $tag = LibraryTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
        }
        
        $resource->tags()->sync($tagIds);
    }

    /**
     * Statistics for Library Dashboard.
     */
    public function getDashboardStats()
    {
        return [
            'total' => LibraryResource::count(),
            'published' => LibraryResource::where('status', 'published')->count(),
            'drafts' => LibraryResource::where('status', 'draft')->count(),
            'featured' => LibraryResource::where('is_featured', true)->count(),
            'types_count' => LibraryResourceType::count(),
            'latest_additions' => LibraryResource::latest()->take(5)->get(),
        ];
    }
}
