<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockVisitResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['block_visit_id','block_unit_id','value','notes','created_by','updated_by','deleted_by'];

    public function visit()
    {
        return $this->belongsTo(BlockVisit::class, 'block_visit_id');
    }
}


