<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockWorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    // Archive status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'block_issue_id',
        'issued_from',
        'from_id',
        'block_unit_id',
        'block_building_id',
        'priority_id',
        'issued_date_time',
        'contractor_id',
        'contact_name',
        'contact_mobile',
        'contact_email',
        'preferred_start_date_time',
        'preferred_end_date_time',
        'deadline_date',
        'issued_by',
        'status', // Job status (integer) - kept for backward compatibility
        'archive_status', // Archive status: 'active' or 'archived' (string)
        'acceptance_status',
        'rejection_reason',
        'ref_no',
        'repair_category_id',
        'issue',
        'note_for_access',
        'pdf_path',
        'pdf_name',
        'block_visit_id',
        'block_inspection_id',
        'comment',
        'is_mobile',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'block_id' => 'integer',
        'block_issue_id' => 'integer',
        'issued_from' => 'integer',
        'from_id' => 'integer',
        'block_unit_id' => 'integer',
        'block_building_id' => 'integer',
        'priority_id' => 'integer',
        'issued_date_time' => 'datetime',
        'contractor_id' => 'integer',
        'preferred_start_date_time' => 'datetime',
        'preferred_end_date_time' => 'datetime',
        'deadline_date' => 'date',
        'issued_by' => 'integer',
        'status' => 'integer', // Job status (job_status_id)
        'repair_category_id' => 'integer',
        'block_visit_id' => 'integer',
        'block_inspection_id' => 'integer',
        'is_mobile' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the block that owns the work order.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the block issue that owns the work order.
     */
    public function blockIssue()
    {
        return $this->belongsTo(BlockIssue::class);
    }

    /**
     * Get the block unit for the work order.
     */
    public function blockUnit()
    {
        return $this->belongsTo(BlockUnit::class);
    }

    /**
     * Get the contractor (user) for the work order.
     * @deprecated Use contractCompany() instead
     */
    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    /**
     * Get the contract company (1_contractors table) for the work order.
     * @deprecated For work orders created via create/edit form, contractor_id may store contract_companies.id; use contractCompanyEntity() or getContractorCompanyDisplayName() instead.
     */
    public function contractCompany()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    /**
     * Get the contract company (contract_companies table) when contractor_id stores a contract_company_id.
     */
    public function contractCompanyEntity()
    {
        return $this->belongsTo(ContractCompany::class, 'contractor_id');
    }

    /**
     * Resolve the contractor company display name for list/show views.
     * contractor_id may store: users.id (property manager), contract_companies.id (contract company), or 1_contractors.id (legacy).
     */
    public function getContractorCompanyDisplayName(): ?string
    {
        if (!$this->contractor_id) {
            return null;
        }
        // 1) Property manager (User) – form uses property_manager_id
        if ($this->contractor && $this->contractor->userType && $this->contractor->userType->name === 'Property manager') {
            return $this->contractor->name . ' (Property Manager)';
        }
        // 2) Contract company (contract_companies) – form uses contract_company_id
        if ($this->contractCompanyEntity) {
            return $this->contractCompanyEntity->company_name ?? null;
        }
        // 3) Legacy contractor (1_contractors)
        if ($this->contractCompany) {
            return $this->contractCompany->name ?? null;
        }
        return null;
    }

    /**
     * Get the block building for the work order.
     */
    public function blockBuilding()
    {
        return $this->belongsTo(BlockBuilding::class);
    }

    /**
     * Get the priority for the work order.
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    /**
     * Get the job status for the work order.
     */
    public function jobStatus()
    {
        return $this->belongsTo(JobStatus::class, 'status');
    }

    /**
     * Get the images for the work order.
     */
    public function images()
    {
        return $this->hasMany(BlockWorkOrderImage::class);
    }

    /**
     * Get the notes for the work order.
     */
    public function notes()
    {
        return $this->hasMany(BlockWorkOrderNote::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the team members for the work order.
     */
    public function teamMembers()
    {
        return $this->hasMany(BlockWorkOrderTeam::class)->orderBy('is_lead', 'desc')->orderBy('created_at', 'asc');
    }

    /**
     * Check if the given user is a team member of this work order.
     * Team member = the work order's contractor (when contractor_id is a user id) or a user in block_work_order_teams.
     * For an accepted job, only team members may change status (start, pause, resume, complete).
     */
    public function isUserTeamMember(User $user): bool
    {
        if ($this->contractor_id === (int) $user->id) {
            return true;
        }
        return $this->teamMembers()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the user who issued the work order.
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the creator of the work order.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the work order.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the logs for the work order.
     */
    public function logs()
    {
        return $this->hasMany(BlockWorkOrderLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Boot method to automatically generate ref_no
     */
    protected static function boot()
    {
        parent::boot();

        // Set temporary placeholder if ref_no is empty (required for NOT NULL field)
        static::creating(function ($blockWorkOrder) {
            if (empty($blockWorkOrder->ref_no)) {
                $blockWorkOrder->ref_no = 'TEMP'; // Temporary placeholder
            }
        });

        // Generate actual ref_no after record is created (to access ID)
        static::created(function ($blockWorkOrder) {
            if ($blockWorkOrder->ref_no === 'TEMP' || empty($blockWorkOrder->ref_no)) {
                $blockWorkOrder->ref_no = self::generateRefNo($blockWorkOrder->id);
                $blockWorkOrder->saveQuietly(); // Save without triggering events
            }
        });
    }

    /**
     * Generate a unique reference number
     * Format: YYMM + ID (e.g., 2601 + 1 = 26011)
     * 
     * @param int $id The database ID of the work order
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
     * Scope a query to only include active work orders (not deleted and archive_status is active or null).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
                    ->where(function($q) {
                        $q->where('archive_status', self::STATUS_ACTIVE)
                          ->orWhereNull('archive_status'); // Handle existing records without archive_status
                    });
    }

    /**
     * Scope a query to only include archived work orders.
     */
    public function scopeArchived($query)
    {
        return $query->where('archive_status', self::STATUS_ARCHIVED);
    }

    /**
     * Check if work order has been updated (accepted or status changed from assigned).
     * Returns true if work order has been updated and should only be archived.
     * Excludes creation logs and images uploaded during creation.
     */
    public function hasBeenUpdated(): bool
    {
        // Check if acceptance_status is 'accepted' (work order has been accepted)
        if ($this->acceptance_status === 'accepted') {
            return true;
        }

        // Check if status is not 1 (assigned/pending state) - work order has been updated
        // Status 1 = Pending/Assigned, any other status means it's been updated
        if ($this->status !== 1) {
            return true;
        }

        // Check if work order has notes (any note indicates update)
        if ($this->notes()->count() > 0) {
            return true;
        }

        // Check if work order has team members (indicates update)
        if ($this->teamMembers()->count() > 0) {
            return true;
        }

        // Check for logs excluding creation logs and initial attachment logs
        // Exclude logs created within 10 seconds of work order creation (creation window)
        $creationWindowEnd = $this->created_at ? $this->created_at->copy()->addSeconds(10) : null;
        
        $nonCreationLogs = $this->logs()
            ->where('log_type', '!=', 'created')
            ->when($creationWindowEnd, function($query) use ($creationWindowEnd) {
                // Exclude attachment_added logs created during creation window
                return $query->where(function($q) use ($creationWindowEnd) {
                    $q->where('log_type', '!=', 'attachment_added')
                      ->orWhere('created_at', '>', $creationWindowEnd);
                });
            })
            ->count();
        
        if ($nonCreationLogs > 0) {
            return true;
        }

        // Check for images uploaded after creation (not during creation)
        // Exclude images created within 10 seconds of work order creation
        $nonCreationImages = $this->images()
            ->when($creationWindowEnd, function($query) use ($creationWindowEnd) {
                return $query->where('created_at', '>', $creationWindowEnd);
            })
            ->count();
        
        if ($nonCreationImages > 0) {
            return true;
        }

        // Check if work order has been updated (created_at != updated_at)
        // But allow a small time difference (up to 5 seconds) to account for database timing
        if ($this->created_at && $this->updated_at) {
            $timeDiff = $this->created_at->diffInSeconds($this->updated_at);
            if ($timeDiff > 5) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if work order is archived.
     */
    public function isArchived(): bool
    {
        return $this->archive_status === self::STATUS_ARCHIVED;
    }

    /**
     * Check if work order is active.
     */
    public function isActive(): bool
    {
        return $this->archive_status === self::STATUS_ACTIVE && is_null($this->deleted_at);
    }

    /**
     * Archive the work order (soft delete with archived status).
     */
    public function archive(): bool
    {
        $this->archive_status = self::STATUS_ARCHIVED;
        $this->deleted_by = auth()->id();
        $this->save();
        return $this->delete(); // Soft delete
    }

    /**
     * Get the deleter of the work order.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get the status text.
     */
    public function getStatusTextAttribute()
    {
        // Use the jobStatus relationship if available, otherwise fall back to hardcoded mapping
        if ($this->relationLoaded('jobStatus') && $this->jobStatus) {
            return $this->jobStatus->name;
        }
        
        // Fallback mapping (should match JobStatus table)
        $statuses = [
            1 => 'Scheduled',
            2 => 'In Progress',
            3 => 'Completed',
            4 => 'Cancelled',
            5 => 'On Hold',
            6 => 'Rescheduled',
            7 => 'Rejected',
            8 => 'Accepted'
        ];
        
        return $statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Get the priority text.
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
        
        return $priorities[$this->priority_id] ?? 'Normal';
    }

    /**
     * Get the PDF URL.
     */
    public function getPdfUrlAttribute()
    {
        if ($this->pdf_path && $this->pdf_name) {
            return asset('storage/' . $this->pdf_path . '/' . $this->pdf_name);
        }
        
        return null;
    }
}
