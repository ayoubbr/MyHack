<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function team()
    {
        return $this->hasOne(Team::class);
    }

    public function juryMembers()
    {
        return $this->hasMany(JuryMember::class);
    }
}
