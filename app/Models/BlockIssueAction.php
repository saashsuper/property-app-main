<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockIssueAction extends Model
{
    protected $fillable = [
        'block_issue_id',
        'action_type',
        'description',
        'notes',
        'performed_by',
        'action_date',
        'status',
        'cost',
        'priority',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'action_date' => 'datetime',
        'cost' => 'decimal:2'
    ];

    // Relationships
    public function blockIssue()
    {
        return $this->belongsTo(BlockIssue::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByActionType($query, $type)
    {
        return $query->where('action_type', $type);
    }

    // Action type constants
    const ACTION_TYPES = [
        'inspection' => 'Inspection',
        'repair' => 'Repair',
        'follow_up' => 'Follow Up',
        'resolved' => 'Resolved',
        'escalated' => 'Escalated',
        'maintenance' => 'Maintenance',
        'assessment' => 'Assessment',
        'communication' => 'Communication',
        'documentation' => 'Documentation'
    ];

    // Status constants
    const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];

    // Priority constants
    const PRIORITIES = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
        'critical' => 'Critical'
    ];
}
