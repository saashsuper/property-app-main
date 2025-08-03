<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockIssue extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ref_no',
        'block_id',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'reported_by',
        'issued_from',
        'from_id',
        'contractor_type_id',
        'priority_id',
        'block_unit_id',
        'contact_details',
        'issue',
        'issue_details',
        'contact_name',
        'contact_mobile',
        'contact_email',
        'preferred_start_date_time',
        'preferred_end_date_time',
        'note_for_access',
        'issued_by',
        'block_visit_id',
        'block_inspection_id',
        'issued_date_time',
        'comment',
        'is_mobile',
        'created_by',
        'updated_by',
        'issue_status_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preferred_start_date_time' => 'datetime',
        'preferred_end_date_time' => 'datetime',
        'issued_date_time' => 'datetime',
        'is_mobile' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the block that owns the issue.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the unit that owns the issue.
     */
    public function unit()
    {
        return $this->belongsTo(BlockUnit::class, 'block_unit_id');
    }

    /**
     * Get the user who issued the issue.
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the user who created the issue.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the issue.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

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

        return $priorities[$this->priority ?? $this->priority_id] ?? 'Unknown';
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

        return $statuses[$this->status ?? $this->issue_status_id] ?? 'Unknown';
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

        return $colors[$this->priority ?? $this->priority_id] ?? 'info';
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

        return $colors[$this->status ?? $this->issue_status_id] ?? 'secondary';
    }
}
