<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function hackathon()
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
