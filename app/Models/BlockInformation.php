<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockInformation extends Model
{
    use HasFactory;

    protected $table = 'block_information';

    protected $fillable = [
        'block_id',
        'information_type_id',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the block that owns this information
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the information type
     */
    public function informationType()
    {
        return $this->belongsTo(BlockInformationType::class, 'information_type_id');
    }

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
