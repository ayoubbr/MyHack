<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hackathon extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'place'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function theme()
    {
        return $this->hasMany(Theme::class);
    }

    public function rules()
    {
        return $this->belongsToMany(Theme::class);
    }
}
