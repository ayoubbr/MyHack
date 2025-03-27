<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuryMember extends Model
{
    use HasFactory;

    protected $fillable = ['username', 'pin'];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function jury()
    {
        return $this->belongsTo(Jury::class);
    }
}
