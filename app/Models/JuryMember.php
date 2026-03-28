<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class JuryMember extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = ['username', 'pin'];
    protected $hidden = ['pin'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function jury()
    {
        return $this->belongsTo(Jury::class);
    }
}
