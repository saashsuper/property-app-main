<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockInspectionTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'block_inspection_id',
        'user_id',
        'role',
        'is_lead',
    ];

    protected $casts = [
        'block_inspection_id' => 'integer',
        'user_id' => 'integer',
        'is_lead' => 'boolean',
    ];

    /**
     * Get the block inspection that owns the team member.
     */
    public function blockInspection()
    {
        return $this->belongsTo(BlockInspection::class);
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
