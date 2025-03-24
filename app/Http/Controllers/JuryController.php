<?php

namespace App\Http\Controllers;

use App\Models\Jury;
use App\Models\JuryMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JuryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jury_name' => 'nullable|string'
        ]);

        $jury = Jury::where('name', $request->input('jury_name'))->first();

        do {
            $userName = 'jury_' . Str::random(6);
        } while (JuryMember::where('username', $userName)->exists());

        $randomPin = Hash::make(str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT));
        $juryMember = new JuryMember();
        $juryMember->username = $userName;
        $juryMember->pin = $randomPin;
        $juryMember->jury()->associate($jury);
        $juryMember->save();
        
        return response()->json([
            'message' => 'Compte JuryMember créé avec succès !',
            'jury_member' => $juryMember
        ], 201);
    }
}
