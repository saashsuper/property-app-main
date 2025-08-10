<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockInspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'block_id',
        'ref_no',
        'scheduled_date_time',
        'start_date_time',
        'end_date_time',
        'notes',
        'pdf_path',
        'pdf_name',
        'job_status_id',
        'is_mobile',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'scheduled_date_time' => 'datetime',
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'is_mobile' => 'boolean',
        'job_status_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the block that owns the inspection.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the creator of the inspection.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the inspection.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the deleter of the inspection.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the inspection assets.
     */
    public function inspectionAssets()
    {
        return $this->hasMany(BlockInspectionAsset::class);
    }

    /**
     * Get the inspection teams.
     */
    public function inspectionTeams()
    {
        return $this->hasMany(BlockInspectionTeam::class);
    }

    /**
     * Get the status text.
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            1 => 'Scheduled',
            2 => 'In Progress',
            3 => 'Completed',
            4 => 'Cancelled',
            5 => 'On Hold'
        ];

        return $statuses[$this->job_status_id] ?? 'Unknown';
    }

    /**
     * Get the status color.
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            1 => 'info',
            2 => 'warning',
            3 => 'success',
            4 => 'danger',
            5 => 'secondary'
        ];

        return $colors[$this->job_status_id] ?? 'secondary';
    }

    /**
     * Scope for active inspections.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('job_status_id', [1, 2, 5]);
    }

    /**
     * Scope for completed inspections.
     */
    public function scopeCompleted($query)
    {
        return $query->where('job_status_id', 3);
    }

    /**
     * Generate reference number.
     */
    public static function generateRefNo()
    {
        $prefix = 'INSP';
        $year = date('Y');
        $month = date('m');
        
        $lastInspection = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInspection ? intval(substr($lastInspection->ref_no, -4)) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
