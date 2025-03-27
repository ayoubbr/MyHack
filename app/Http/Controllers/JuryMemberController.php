<?php

namespace App\Http\Controllers;

use App\Models\Jury;
use App\Models\JuryMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JuryMemberController extends Controller
{


    public function index(Request $request)
    {
        if ($request->has('jury_id')) {
            $juryMembers = JuryMember::where('jury_id', $request->jury_id)
                ->with('jury')
                ->get();
        } else {
            $juryMembers = JuryMember::with('jury')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $juryMembers
        ]);
    }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'jury_name' => 'nullable|string'
    //     ]);

    //     $jury = Jury::where('name', $request->input('jury_name'))->first();

    //     do {
    //         $userName = 'jury_' . Str::random(6);
    //     } while (JuryMember::where('username', $userName)->exists());

    //     $randomPin = Hash::make(str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT));
    //     $juryMember = new JuryMember();
    //     $juryMember->username = $userName;
    //     $juryMember->pin = $randomPin;
    //     $juryMember->jury()->associate($jury);
    //     $juryMember->save();

    //     return response()->json([
    //         'message' => 'Compte JuryMember créé avec succès !',
    //         'jury_member' => $juryMember
    //     ], 201);
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'jury_id' => 'required|exists:juries,id',
            'pin' => 'nullable|string|min:4|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pin = $request->pin ?? Str::random(4);

        $juryMember = JuryMember::create([
            'username' => $request->username,
            'jury_id' => $request->jury_id,
            'pin' => $pin,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jury member created successfully',
            'data' => [
                'jury_member' => $juryMember,
                'pin' => $pin
            ]
        ], 201);
    }

    
    public function show($id)
    {
        $juryMember = JuryMember::with(['jury', 'notes'])->find($id);

        if (!$juryMember) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jury member not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $juryMember
        ]);
    }


    public function update(Request $request, $id)
    {
        $juryMember = JuryMember::find($id);

        if (!$juryMember) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jury member not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|string|max:255',
            'jury_id' => 'sometimes|required|exists:juries,id',
            'pin' => 'nullable|string|min:4|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['username', 'jury_id']);

        if ($request->has('pin')) {
            $updateData['pin'] = $request->pin;
        }

        $juryMember->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Jury member updated successfully',
            'data' => $juryMember
        ]);
    }

  
    public function delete($id)
    {
        $juryMember = JuryMember::find($id);

        if (!$juryMember) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jury member not found'
            ], 404);
        }

        $juryMember->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jury member deleted successfully'
        ]);
    }


   
}
