<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RuleController extends Controller
{
    public function index()
    {
        $rules = Rule::all();

        return response()->json([
            'rules' => $rules
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $rule = Rule::create([
            'name' => $request['name']
        ]);

        return response()->json([
            'rule' => $rule
        ]);
    }

    public function update(Request $request, $ruleId)
    {
        $rule = Rule::find($ruleId);
        $rule->name = $request->name;

        $rule->save();

        return response()->json([
            'rule' => $rule
        ]);
    }

    public function delete($ruleId)
    {
        $rule = Rule::find($ruleId);
        $rule->delete();

        return response()->json([
            'message' => 'deleted successfully!',
            'rule' => $rule
        ]);
    }
}
