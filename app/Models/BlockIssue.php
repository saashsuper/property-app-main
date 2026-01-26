<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockIssue extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants for archive/active
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

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
        'status', // Archive status: 'active' or 'archived'
        'assigned_to',
        'reported_by',
        'issued_from',
        'from_id',
        'contractor_type_id',
        'priority_id',
        'block_unit_id',
        'block_building_id',
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
        'deleted_by',
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
        'deleted_by' => 'integer',
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
     * Get the block building that owns the issue.
     */
    public function blockBuilding()
    {
        return $this->belongsTo(BlockBuilding::class, 'block_building_id');
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

        // Set temporary placeholder if ref_no is empty (required for NOT NULL field)
        static::creating(function ($blockIssue) {
            if (empty($blockIssue->ref_no)) {
                $blockIssue->ref_no = 'TEMP'; // Temporary placeholder
            }
        });

        // Generate actual ref_no after record is created (to access ID)
        static::created(function ($blockIssue) {
            if ($blockIssue->ref_no === 'TEMP' || empty($blockIssue->ref_no)) {
                $blockIssue->ref_no = self::generateRefNo($blockIssue->id);
                $blockIssue->saveQuietly(); // Save without triggering events
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

    public function logs()
    {
        return $this->hasMany(IssueLog::class, 'block_issue_id');
    }

    /**
     * Scope for active issues (not deleted and status is active).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
                    ->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include archived issues.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    /**
     * Check if issue has any related work orders.
     * Returns true if issue has at least one work order.
     */
    public function hasWorkOrders(): bool
    {
        return $this->workOrders()->count() > 0;
    }

    /**
     * Check if issue has any related site visits.
     * Returns true if issue has at least one site visit.
     */
    public function hasSiteVisits(): bool
    {
        return $this->relatedSiteVisits()->count() > 0;
    }

    /**
     * Check if issue has any related actions.
     * Returns true if issue has at least one action.
     */
    public function hasActions(): bool
    {
        return $this->actions()->count() > 0;
    }

    /**
     * Check if issue has any related entities (work orders, site visits, or actions).
     * Returns true if issue has at least one related entity.
     */
    public function hasRelatedEntities(): bool
    {
        return $this->hasWorkOrders() || $this->hasSiteVisits() || $this->hasActions();
    }

    /**
     * Check if issue is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Check if issue is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && is_null($this->deleted_at);
    }

    /**
     * Archive the issue (soft delete with archived status).
     */
    public function archive(): bool
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->deleted_by = auth()->id();
        $this->save();
        return $this->delete(); // Soft delete
    }

    /**
     * Get the deleter of the issue.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
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

        return $priorities[$this->priority_id] ?? 'Unknown';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        // Use the issueStatus relationship if loaded, otherwise use issue_status_id
        $statusId = $this->issueStatus?->value ?? $this->issue_status_id;
        
        $statuses = [
            1 => 'Created',
            2 => 'In Progress',
            3 => 'Work Order',
            4 => 'Completed',
            5 => 'Invoiced'
        ];

        return $statuses[$statusId] ?? 'Unknown';
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
        // Use the issueStatus relationship if loaded, otherwise use issue_status_id
        $statusId = $this->issueStatus?->value ?? $this->issue_status_id;
        
        $colors = [
            1 => 'warning',    // Created - btn-warning (yellow)
            2 => 'primary',    // In Progress - btn-primary (blue)
            3 => 'secondary',  // Work Order - btn-secondary (gray)
            4 => 'success',    // Completed - btn-success (green)
            5 => 'dark'        // Invoiced - btn-dark (dark gray/black)
        ];

        return $colors[$statusId] ?? 'secondary';
    }
}
