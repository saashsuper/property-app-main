<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockInformationType extends Model
{
    use HasFactory;

    protected $table = 'block_information_types';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the display name for the information type
     */
    public function getDisplayNameAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->name));
    }
}
