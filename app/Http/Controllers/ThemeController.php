<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::all();

        return response()->json([
            'themes' => $themes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $theme = Theme::create([
            'name' => $request['name'],
            'description' => $request['description']
        ]);

        return response()->json([
            'theme' => $theme
        ]);
    }

    public function update(Request $request, $themeId)
    {
        $theme = Theme::find($themeId);
        $theme->name = $request->name;
        $theme->description = $request->description;

        $theme->save();

        return response()->json([
            'theme' => $theme
        ]);
    }

    public function delete($themeId)
    {
        $theme = Theme::find($themeId);
        $theme->delete();

        return response()->json([
            'message' => 'deleted successfully!',
            'theme' => $theme
        ]);
    }
}
