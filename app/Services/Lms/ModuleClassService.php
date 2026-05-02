<?php

namespace App\Services\Lms;

use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsModuleClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ModuleClassService
{
    public function listForModule(LmsModule $module)
    {
        return $module->classes()->withCount(['lessons', 'resources'])->get();
    }

    public function createClass(LmsModule $module, array $data)
    {
        $data['lms_module_id'] = $module->id;
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle unique slug within module
        $originalSlug = $data['slug'];
        $count = 1;
        while (LmsModuleClass::where('lms_module_id', $module->id)->where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count++;
        }

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $module->classes()->max('sort_order') + 1;
        }

        return LmsModuleClass::create($data);
    }

    public function updateClass(LmsModuleClass $class, array $data)
    {
        if (isset($data['title']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['title']);
            
            $originalSlug = $data['slug'];
            $count = 1;
            while (LmsModuleClass::where('lms_module_id', $class->lms_module_id)
                ->where('slug', $data['slug'])
                ->where('id', '!=', $class->id)
                ->exists()) {
                $data['slug'] = $originalSlug . '-' . $count++;
            }
        }

        $class->update($data);
        return $class;
    }

    public function deleteClass(LmsModuleClass $class)
    {
        // Project convention for child content (lessons/resources)
        // Usually, we don't want to delete them silently. 
        // Here we'll just soft delete the class. 
        // The foreign keys have nullOnDelete, so children will be detached.
        return $class->delete();
    }

    public function reorderClasses(LmsModule $module, array $orderedIds)
    {
        return DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                LmsModuleClass::where('id', $id)->update(['sort_order' => $index]);
            }
        });
    }

    public function moveLegacyContentToClass(LmsModule $module, string $classTitle = 'Introduction Class')
    {
        return DB::transaction(function () use ($module, $classTitle) {
            $class = $this->createClass($module, [
                'title' => $classTitle,
                'description' => 'Automatically created to hold legacy module content.',
                'is_published' => true
            ]);

            $module->lessons()->whereNull('lms_module_class_id')->update(['lms_module_class_id' => $class->id]);
            $module->resources()->whereNull('lms_module_class_id')->update(['lms_module_class_id' => $class->id]);

            return $class;
        });
    }

    public function getClassWithContent(LmsModuleClass $class)
    {
        return $class->load(['lessons', 'resources']);
    }
}
