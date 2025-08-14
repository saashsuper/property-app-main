<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockInformationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'block_information_types';

    protected $fillable = [
        'name',
        'description',
        'category',
        'data_type',
        'validation_rules',
        'is_required',
        'is_active',
        'sort_order',
        'default_value',
        'options',
        'help_text',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'options' => 'array',
        'validation_rules' => 'array',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

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

    /**
     * Scope to get only active information types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get information by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get required information types
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get validation rules as array
     */
    public function getValidationRulesArrayAttribute()
    {
        if (is_string($this->validation_rules)) {
            return json_decode($this->validation_rules, true) ?? [];
        }
        return $this->validation_rules ?? [];
    }

    /**
     * Check if field is required
     */
    public function isRequired()
    {
        return $this->is_required;
    }

    /**
     * Check if field is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get options as array for select fields
     */
    public function getOptionsArrayAttribute()
    {
        if (is_string($this->options)) {
            return json_decode($this->options, true) ?? [];
        }
        return $this->options ?? [];
    }

    /**
     * Get data type label
     */
    public function getDataTypeLabelAttribute()
    {
        $labels = [
            'text' => 'Text Input',
            'number' => 'Number Input',
            'date' => 'Date Picker',
            'boolean' => 'Yes/No',
            'select' => 'Single Select',
            'multiselect' => 'Multiple Select',
            'textarea' => 'Text Area',
            'file' => 'File Upload',
            'email' => 'Email Input',
            'url' => 'URL Input',
        ];

        return $labels[$this->data_type] ?? ucfirst($this->data_type);
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->category));
    }
}
