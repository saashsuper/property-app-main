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
        'issue_type',
        'issue_details',
        'contact_name',
        'contact_mobile',
        'contact_email',
        'contact_method_id',
        'salutation',
        'phone_number',
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
     * Get the block unit that owns the issue (alias for unit).
     */
    public function blockUnit()
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
     * Boot method to automatically generate ref_no
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blockIssue) {
            if (empty($blockIssue->ref_no)) {
                $blockIssue->ref_no = self::generateRefNo();
            }
        });
    }

    /**
     * Generate a unique reference number
     */
    protected static function generateRefNo()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last issue number for this month
        $lastIssue = self::where('ref_no', 'like', "{$year}-{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastIssue) {
            // Extract the number part and increment
            $parts = explode('-', $lastIssue->ref_no);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format: 2025-10-001
        return sprintf('%s-%s-%03d', $year, $month, $newNumber);
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
     * Get the contact method for the issue
     */
    public function contactMethod()
    {
        return $this->belongsTo(ContactMethod::class, 'contact_method_id');
    }

    /**
     * Get the priority for the issue
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    /**
     * Get the issue status for the issue
     */
    public function issueStatus()
    {
        return $this->belongsTo(IssueStatus::class, 'issue_status_id');
    }

    /**
     * Get the issue type for the issue
     */
    public function issueType()
    {
        return $this->belongsTo(IssueType::class, 'issue_type', 'name');
    }

    /**
     * Get the images for the issue
     */
    public function images()
    {
        return $this->hasMany(BlockIssueImage::class, 'block_issue_id');
    }

    /**
     * Get the work orders for the issue
     */
    public function workOrders()
    {
        return $this->hasMany(BlockWorkOrder::class, 'block_issue_id');
    }

    /**
     * Get the site visit for the issue
     */
    public function siteVisit()
    {
        return $this->belongsTo(BlockVisit::class, 'block_visit_id');
    }

    /**
     * Get all site visits created for this specific issue
     */
    public function relatedSiteVisits()
    {
        return $this->hasMany(BlockVisit::class, 'block_issue_id');
    }

    /**
     * Get all actions for this block issue
     */
    public function actions()
    {
        return $this->hasMany(BlockIssueAction::class, 'block_issue_id');
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
            1 => 'Created',
            2 => 'In Progress',
            3 => 'Work Order',
            4 => 'Completed',
            5 => 'Invoiced'
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
            1 => 'warning',    // Created - btn-warning
            2 => 'primary',    // In Progress - btn-primary
            3 => 'secondary',  // Work Order - btn-secondary
            4 => 'success',    // Completed - btn-success
            5 => 'light'       // Invoiced - btn-light
        ];

        return $colors[$this->status ?? $this->issue_status_id] ?? 'secondary';
    }
}
