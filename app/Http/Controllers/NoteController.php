<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Team;
use App\Models\JuryMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::query();
        
        if ($request->has('team_id')) {
            $query->where('team_id', $request->team_id);
        }
        
        if ($request->has('jury_member_id')) {
            $query->where('jury_member_id', $request->jury_member_id);
        }
        
        $notes = $query->with(['juryMember', 'team'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $notes
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric|min:0|max:10',
            'comment' => 'nullable|string',
            'jury_member_id' => 'required|exists:jury_members,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $juryMember = JuryMember::find($request->jury_member_id);
        $team = Team::find($request->team_id);
        
        if ($team->jury_id !== $juryMember->jury_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'This team is not assigned to your jury'
            ], 403);
        }

        $existingNote = Note::where('jury_member_id', $request->jury_member_id)
                           ->where('team_id', $request->team_id)
                           ->first();
                           
        if ($existingNote) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already rated this team',
                'data' => $existingNote
            ], 409);
        }

        $note = Note::create([
            'value' => $request->value,
            'comment' => $request->comment,
            'jury_member_id' => $request->jury_member_id,
            'team_id' => $request->team_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Note created successfully',
            'data' => $note->load(['juryMember', 'team'])
        ], 201);
    }

   
    public function show($id)
    {
        $note = Note::with(['juryMember', 'team'])->find($id);

        if (!$note) {
            return response()->json([
                'status' => 'error',
                'message' => 'Note not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $note
        ]);
    }


    public function update(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'status' => 'error',
                'message' => 'Note not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'value' => 'sometimes|required|numeric|min:0|max:10',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $note->update($request->only(['value', 'comment']));

        return response()->json([
            'status' => 'success',
            'message' => 'Note updated successfully',
            'data' => $note->load(['juryMember', 'team'])
        ]);
    }

  
    public function delete($id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'status' => 'error',
                'message' => 'Note not found'
            ], 404);
        }

        $note->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Note deleted successfully'
        ]);
    }

   
    public function teamAverage($teamId)
    {
        $team = Team::find($teamId);

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found'
            ], 404);
        }

        $notes = Note::where('team_id', $teamId)->get();
        
        if ($notes->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'team_id' => $teamId,
                    'average_score' => 0,
                    'total_notes' => 0
                ]
            ]);
        }

        $averageScore = $notes->avg('value');
        $totalNotes = $notes->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'team_id' => $teamId,
                'average_score' => round($averageScore, 2),
                'total_notes' => $totalNotes
            ]
        ]);
    }
}