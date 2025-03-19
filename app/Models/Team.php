<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function juries()
    {
        return $this->belongsTo(Jury::class);
    }
}
