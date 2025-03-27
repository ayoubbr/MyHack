<?php

namespace App\Http\Controllers;

use App\Models\Jury;
use App\Models\JuryMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

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


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'jury_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $pinHashed = Hash::make($pin);
        $jury = Jury::where('name', $request->input('jury_name'))->first();

        $juryMember = JuryMember::create([
            'username' => $request->username,
            'pin' => $pinHashed,
        ]);

        $juryMember->jury()->associate($jury);
        $juryMember->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Jury member created successfully',
            'data' => [
                'jury_member' => $juryMember,
                'pin' => $pin
            ]
        ], 201);
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


    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'pin' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $juryMember = JuryMember::where('username', $request->username)->first();

        if (!$juryMember || $juryMember->pin !== $request->pin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = JWTAuth::claims()->fromUser($juryMember);

        return response()->json([
            'status' => 'success',
            'message' => 'Authentication successful',
            'data' => [
                'jury_member' => $juryMember->load('jury'),
                'token' => $token
            ]
        ]);
    }
}
