<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'country_name',
        'phonecode'
    ];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
