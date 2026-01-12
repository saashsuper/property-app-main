<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'value',
        'btn_class',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the issues associated with this priority.
     */
    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    /**
     * Get the block issues associated with this priority.
     */
    public function blockIssues()
    {
        return $this->hasMany(BlockIssue::class);
    }

    /**
     * Get the block work orders associated with this priority.
     */
    public function blockWorkOrders()
    {
        return $this->hasMany(BlockWorkOrder::class);
    }

    /**
     * Scope to get priorities ordered by value.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('value', 'asc');
    }

    /**
     * Get the CSS button class for this priority.
     */
    public function getButtonClassAttribute()
    {
        return $this->btn_class ?? 'secondary';
    }

    /**
     * Get the formatted label with value.
     */
    public function getFormattedLabelAttribute()
    {
        return "{$this->label} ({$this->value})";
    }
}

