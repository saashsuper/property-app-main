<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'block_id','ref_no','scheduled_date_time','start_date_time','end_date_time','job_reason_id',
        'notes','comment','pdf_path','pdf_name','job_status_id','block_visit_action_id','is_mobile',
        'created_by','updated_by','deleted_by'
    ];

    protected $casts = [
        'scheduled_date_time' => 'datetime',
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'is_mobile' => 'boolean',
    ];

    public function block()
    {
        return $this->belongsTo(Block::class);
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
}


