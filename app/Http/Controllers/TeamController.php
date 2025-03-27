<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Hackathon;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{

    public function index(Request $request)
    {
        $teams = Team::with(['users', 'hackathon', 'theme'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $teams
        ]);
    }


    public function show($id)
    {
        $team = Team::with(['users', 'hackathon'])->find($id);

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Équipe non trouvée'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $team
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'hackathon_name' => 'required',
            'theme_name' => 'required',
            // 'project' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // if (auth()->user()->role->role_name != 'participant') {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Unauthorized action',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        $hackathon = Hackathon::where('name', $request->hackathon_name)->first();
        if (!$hackathon || strtotime($hackathon->date) < time()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Les inscriptions pour ce hackathon sont fermées'
            ], 400);
        }

        $theme = Theme::where('name', $request->theme_name)->first();
        if (!$theme) {
            return response()->json([
                'status' => 'error',
                'message' => 'Theme not found'
            ], 400);
        }

        $team = Team::create([
            'name' => $request->name,
            'status' => 'pending',
            // 'project' => $request->project,
        ]);

        $team->hackathon()->associate($hackathon);
        $team->save();
        // $user = Auth::user();
        // $team->users()->associate($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Team created successfully',
            'data' => $team->load('users')
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found'
            ], 404);
        }

        // $user = Auth::user();
        // if (!$team->users->contains($user->id) && $user->role->role_name !== 'organisateur' ) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Vous n\'êtes pas autorisé à modifier cette équipe'
        //     ], 403);
        // }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'project' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $team->update($request->only(['name', 'project']));

        return response()->json([
            'status' => 'success',
            'message' => 'Team updated succesfully',
            'data' => $team->load('users')
        ]);
    }


    public function delete($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found'
            ], 404);
        }

        $user = Auth::user();
        if ($user->role->role_name !== 'organisateur') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action'
            ], 403);
        }

        $team->users()->detach();

        $team->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Équipe supprimée avec succès'
        ]);
    }


    public function join($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found'
            ], 404);
        }

        $user = Auth::user();

        $userTeams = $user->team;
        foreach ($userTeams as $userTeam) {
            if ($userTeam->hackathon_id === $team->hackathon_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You already in a team in this hackathon'
                ], 400);
            }
        }

        $hackathon = $team->hackathon;
        if (strtotime($hackathon->date) < time()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Inscription are closed in this hackathon'
            ], 400);
        }

        if (!$team->status = "approved" && Auth::user()->role->role_name == 'participant') {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not approved'
            ], 400);
        }

        $team->users()->attach($user->id);

        return response()->json([
            'status' => 'success',
            'message' => 'You are in the Team',
            'data' => $team->load('users')
        ]);
    }


    public function leave($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found'
            ], 404);
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur est membre de l'équipe
        if (!$team->users->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas membre de cette équipe'
            ], 400);
        }

        // Retirer l'utilisateur de l'équipe
        $team->users()->detach($user->id);

        // Si l'équipe n'a plus de membres, la supprimer
        if ($team->users()->count() === 0) {
            $team->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Vous avez quitté l\'équipe et elle a été supprimée car elle était vide'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Vous avez quitté l\'équipe avec succès'
        ]);
    }

    public function approve($id)
    {
        $user = Auth::user();
        if ($user->role->role_name !== 'organisateur') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action'
            ], 403);
        }

        $team = Team::with('users')->find($id);

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found'
            ], 404);
        }

        if ($team->status != 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'This team already traited'
            ], 400);
        }

        $team->status = 'approved';
        $team->save();

        //todo notification

        return response()->json([
            'status' => 'success',
            'message' => 'Team approved',
            'data' => $team
        ]);
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role->role_name !== 'organisateur') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action'
            ], 403);
        }

        $team = Team::with('users')->find($id);

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Équipe non trouvée'
            ], 404);
        }

        if ($team->status != 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Team already traited'
            ], 400);
        }

        $team->status = 'rejected';
        $team->save();

        // Notification todo

        return response()->json([
            'status' => 'success',
            'message' => 'Team rejected',
            'data' => $team
        ]);
    }

    public function submitProject(Request $request, $id)
    {
        $team = Team::find($id);

        $validated = Validator::make($request->all, [
            'project' => 'required'
        ]);

        $team->project = $validated['project'];
        $team->save();

        return response()->json([
            'status' => 'success',
            "message" => "Project submitted successfully",
            "team"  => $team
        ]);
    }
}
