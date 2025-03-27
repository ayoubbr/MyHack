<?php

namespace App\Http\Controllers;

use App\Models\Jury;
use App\Models\JuryMember;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JuryController extends Controller
{

    public function index()
    {
        $juries = Jury::with('juryMembers')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $juries
        ]);
    }

   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $jury = Jury::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jury created successfully',
            'data' => $jury
        ], 201);
    }

    public function show($id)
    {
        $jury = Jury::with(['juryMembers', 'teams'])->find($id);

        if (!$jury) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jury not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $jury
        ]);
    }

   
    public function update(Request $request, $id)
    {
        $jury = Jury::find($id);

        if (!$jury) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jury not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $jury->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jury updated successfully',
            'data' => $jury
        ]);
    }


    public function delete($id)
    {
        $jury = Jury::find($id);

        if (!$jury) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jury not found'
            ], 404);
        }

        $jury->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jury deleted successfully'
        ]);
    }

  
    public function assignTeams(Request $request, $id)
    {
        $jury = Jury::find($id);

        if (!$jury) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jury not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'team_names' => 'required|array',
            // 'team_ids.*' => 'exists:teams,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        Team::whereIn('name', $request->team_names)
            ->update(['jury_id' => $jury->id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Teams assigned to jury successfully',
            'data' => $jury->load('teams')
        ]);
    }
}
