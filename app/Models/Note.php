<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['value'];

    public function juryMember()
    {
        return $this->belongsTo(JuryMember::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
