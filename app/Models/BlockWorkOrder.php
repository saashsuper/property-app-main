<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockWorkOrder extends Model
{
    use HasFactory, SoftDeletes;

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
        'status',
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
        'status' => 'integer',
        'repair_category_id' => 'integer',
        'block_visit_id' => 'integer',
        'block_inspection_id' => 'integer',
        'is_mobile' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
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
     * Get the block building for the work order.
     */
    public function blockBuilding()
    {
        return $this->belongsTo(BlockBuilding::class);
    }

    /**
     * Get the images for the work order.
     */
    public function images()
    {
        return $this->hasMany(BlockWorkOrderImage::class);
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
     * Scope a query to only include active work orders.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Get the status text.
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            1 => 'Pending',
            2 => 'In Progress',
            3 => 'Completed',
            4 => 'Cancelled',
            5 => 'On Hold'
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
