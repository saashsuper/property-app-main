<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ref_no',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'reported_by',
        'location',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'priority' => 'integer',
        'status' => 'integer',
        'assigned_to' => 'integer',
        'reported_by' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who reported the issue
     */
    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the user assigned to the issue
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created the issue
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the issue
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active issues
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Get priority text
     */
    public function getPriorityTextAttribute()
    {
        $priorities = [
            1 => 'Low',
            2 => 'Normal',
            3 => 'High',
            4 => 'Urgent',
            5 => 'Critical'
        ];

        return $priorities[$this->priority] ?? 'Unknown';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            1 => 'Open',
            2 => 'In Progress',
            3 => 'Resolved',
            4 => 'Closed',
            5 => 'On Hold'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Get priority color class
     */
    public function getPriorityColorAttribute()
    {
        $colors = [
            1 => 'success',
            2 => 'info',
            3 => 'warning',
            4 => 'danger',
            5 => 'dark'
        ];

        return $colors[$this->priority] ?? 'info';
    }

    /**
     * Get status color class
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            1 => 'warning',
            2 => 'info',
            3 => 'success',
            4 => 'secondary',
            5 => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}
