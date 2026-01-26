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
     * Boot method to automatically generate ref_no
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($issue) {
            if (empty($issue->ref_no)) {
                $issue->ref_no = self::generateRefNo($issue->id);
                $issue->saveQuietly(); // Save without triggering events
            }
        });
    }

    /**
     * Generate a unique reference number
     * Format: YYMM + ID (e.g., 2601 + 1 = 26011)
     * 
     * @param int $id The database ID of the issue
     * @return string
     */
    protected static function generateRefNo($id)
    {
        $year = date('y'); // 2-digit year
        $month = date('m'); // 2-digit month
        
        // Format: YYMM + ID
        return $year . $month . $id;
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
     * Get the priority for the issue
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority');
    }

    /**
     * Get the issue status for the issue
     */
    public function issueStatus()
    {
        return $this->belongsTo(IssueStatus::class, 'status');
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
            1 => 'Created',
            2 => 'In Progress',
            3 => 'Work Order',
            4 => 'Completed',
            5 => 'Invoiced'
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
            1 => 'warning',    // Created - btn-warning (yellow)
            2 => 'primary',    // In Progress - btn-primary (blue)
            3 => 'secondary',  // Work Order - btn-secondary (gray)
            4 => 'success',    // Completed - btn-success (green)
            5 => 'dark'        // Invoiced - btn-dark (dark gray/black)
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}
