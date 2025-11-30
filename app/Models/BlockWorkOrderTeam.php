<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockWorkOrderTeam extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_work_order_id',
        'user_id',
        'role',
        'is_lead',
        'added_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'block_work_order_id' => 'integer',
        'user_id' => 'integer',
        'is_lead' => 'boolean',
        'added_by' => 'integer',
    ];

    /**
     * Get the block work order that owns the team member.
     */
    public function blockWorkOrder()
    {
        return $this->belongsTo(BlockWorkOrder::class);
    }

    /**
     * Get the user (team member).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who added this team member.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
