<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'block_id','block_issue_id','block_unit_id','ref_no','scheduled_date_time','start_date_time','end_date_time','job_reason_id',
        'notes','comment','pdf_path','pdf_name','job_status_id','block_visit_action_id','is_mobile',
        'created_by','updated_by','deleted_by'
    ];

    protected $casts = [
        'block_issue_id' => 'integer',
        'scheduled_date_time' => 'datetime',
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'is_mobile' => 'boolean',
    ];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function blockIssue()
    {
        return $this->belongsTo(BlockIssue::class);
    }

    public function blockUnit()
    {
        return $this->belongsTo(BlockUnit::class);
    }

    public function images()
    {
        return $this->hasMany(BlockVisitImage::class);
    }

    public function results()
    {
        return $this->hasMany(BlockVisitResult::class);
    }

    public function team()
    {
        return $this->hasMany(BlockVisitTeam::class);
    }

    public function jobReason()
    {
        return $this->belongsTo(JobReason::class);
    }

    public function jobStatus()
    {
        return $this->belongsTo(JobStatus::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Boot method to automatically generate ref_no
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($blockVisit) {
            if (empty($blockVisit->ref_no)) {
                $blockVisit->ref_no = self::generateRefNo($blockVisit->id);
                $blockVisit->saveQuietly(); // Save without triggering events
            }
        });
    }

    /**
     * Generate a unique reference number
     * Format: YYMM + ID (e.g., 2601 + 1 = 26011)
     * 
     * @param int $id The database ID of the visit
     * @return string
     */
    protected static function generateRefNo($id)
    {
        $year = date('y'); // 2-digit year
        $month = date('m'); // 2-digit month
        
        // Format: YYMM + ID
        return $year . $month . $id;
    }
}


