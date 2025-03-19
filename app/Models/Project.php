<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'link_github'];

    public function team()
    {
        return $this->hasMany(Team::class);
    }

    public function themes()
    {
        return $this->belongsTo(Theme::class);
    }
}
