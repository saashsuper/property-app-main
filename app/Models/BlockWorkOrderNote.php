<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockWorkOrderNote extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_work_order_id',
        'note',
        'note_type',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'block_work_order_id' => 'integer',
        'created_by' => 'integer',
    ];

    /**
     * Get the block work order that owns the note.
     */
    public function blockWorkOrder()
    {
        return $this->belongsTo(BlockWorkOrder::class);
    }

    /**
     * Get the user who created the note.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
