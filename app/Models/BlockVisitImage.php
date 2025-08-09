<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockVisitImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['block_visit_id','image_path','s3_status'];

    public function visit()
    {
        return $this->belongsTo(BlockVisit::class, 'block_visit_id');
    }
}


