<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobReason extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function blockVisits()
    {
        return $this->hasMany(BlockVisit::class);
    }
}
