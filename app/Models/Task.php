<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'ai_summary',
        'ai_priority',
    ];

    protected function casts(): array
    {
        return [
            'priority' => \App\Enums\PriorityEnum::class,
            'status' => \App\Enums\StatusEnum::class,
            'due_date' => 'date',
        ];
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['priority'] ?? null, function ($query, $priority) {
            $query->where('priority', $priority);
        })->when($filters['assigned_to'] ?? null, function ($query, $assignedTo) {
            $query->where('assigned_to', $assignedTo);
        });
    }
}
