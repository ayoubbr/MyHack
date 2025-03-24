<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
use App\Models\Rule;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HackathonController extends Controller
{
    public function index()
    {
        $hackathon = Hackathon::all();

        return response()->json([
            'hackathon' => $hackathon
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'date' => 'required|date',
            'place' => 'required',
            'themes' => 'required',
            'rules' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $hackathon = Hackathon::create([
            'name' => $request['name'],
            'date' => $request['date'],
            'place' => $request['place']
        ]);

        foreach ($request->themes as $key => $theme_name) {
            $theme = Theme::where('name', $theme_name)->first();
            $theme->hackathon()->associate($hackathon);
            $theme->save();
        }

        foreach ($request->rules as $key => $rule_name) {
            $rule = Rule::where('name', $rule_name)->first();
            $rule->hackathons()->attach($hackathon);
        }

        return response()->json([
            'hackthon' => $hackathon
        ]);
    }

    public function update(Request $request, $hackthonId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'date' => 'required|date',
            'place' => 'required',
            'themes' => 'required',
            'rules' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $hackathon = Hackathon::create([
            'name' => $request['name'],
            'date' => $request['date'],
            'place' => $request['place']
        ]);

        foreach ($request->themes as $key => $theme_name) {
            $theme = Theme::where('name', $theme_name)->first();
            $theme->hackathon()->associate($hackathon);
            $theme->save();
        }

        foreach ($request->rules as $key => $rule_name) {
            $rule = Rule::where('name', $rule_name)->first();
            $rule->hackathons()->attach($hackathon);
        }

        return response()->json([
            'hackthon' => $hackathon
        ]);
    }

    public function delete($hackthonId)
    {
        $hackthon = Hackathon::find($hackthonId);
        $hackthon->delete();

        return response()->json([
            'message' => 'deleted successfully!',
            'hackthon' => $hackthon
        ]);
    }
}
