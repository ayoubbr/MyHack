<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hackathon extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'date', 'place'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class);
    }
}
