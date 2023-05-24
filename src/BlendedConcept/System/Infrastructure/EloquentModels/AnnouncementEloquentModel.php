<?php

declare(strict_types=1);

namespace Src\BlendedConcept\System\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Src\BlendedConcept\User\Infrastructure\UserEloquentModel;
use Src\BlendedConcept\System\Domain\Model\Organization;

class AnnouncementEloquentModel extends Model
{
    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'message',
        'created_by',
        'trigger_on',
        'send_to'
    ];

    public array $rules = [
        'title' => 'required',
        'message' => 'required',
        'created_by' => 'required',
        'trigger_on' => 'required',
        'send_to' => 'required'
    ];
    public function created_by()
    {
        return $this->belongsTo(Organization::class, 'created_by', 'id');
    }

    public function send_to()
    {
        return $this->belongsTo(UserEloquentModel::class, 'send_to', 'id');
    }

    public function scopeFilter($query, $filters)
    {
        $query->when($filters['name'] ?? false, function ($query, $name) {
            $query->where('title', 'like', '%' . $name . '%');
        });
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        });
    }
}
