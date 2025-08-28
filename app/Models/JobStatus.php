<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_updated'];

    protected $casts = [
        'is_updated' => 'boolean',
    ];

    public function blockVisits()
    {
        return $this->hasMany(BlockVisit::class);
    }
}
