<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class LmsResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lms_resources';

    protected $fillable = [
        'lms_module_id',
        'lms_module_class_id',
        'title',
        'short_description',
        'file_path',
        'sort_order',
        'is_published',
        'is_members_only',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_members_only' => 'boolean',
        'sort_order' => 'integer',
        'lms_module_id' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }

    public function class()
    {
        return $this->belongsTo(LmsModuleClass::class, 'lms_module_class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if user can access this resource.
     */
    public function canAccess(?User $user): bool
    {
        if (!$this->is_members_only) {
            return true;
        }

        return $user && $user->isMember();
    }
}
