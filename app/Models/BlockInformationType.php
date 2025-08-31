<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Scope to get only active information types
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Scope to order information types by name
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }
}
