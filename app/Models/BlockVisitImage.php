<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockVisitImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['block_visit_id','image_path','image_name','s3_status'];

    public function visit()
    {
        return $this->belongsTo(BlockVisit::class, 'block_visit_id');
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            // Check if it's a full URL (for S3 or external storage)
            if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
                return $this->image_path;
            }
            
            // Check if image_name exists (new format)
            if ($this->image_name) {
                return asset('storage/' . $this->image_path . '/' . $this->image_name);
            }
            
            // Old format - path includes filename
            return asset('storage/' . $this->image_path);
        }
        
        return null;
    }

    /**
     * Get the image filename for display.
     */
    public function getDisplayNameAttribute()
    {
        if ($this->image_name) {
            // Remove timestamp prefix and show original name if possible
            $parts = explode('_', $this->image_name);
            if (count($parts) > 2) {
                array_shift($parts); // Remove timestamp
                array_shift($parts); // Remove random string
                return implode('_', $parts);
            }
            return $this->image_name;
        }
        
        if ($this->image_path) {
            return basename($this->image_path);
        }
        
        return 'Image';
    }
}


