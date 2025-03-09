<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'weeks',
        'current_week',
        'completed',
        'winner'
    ];

    public function fixtures()
    {
        return $this->hasMany(Fixture::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }



}

