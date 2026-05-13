<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotHappenReason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    public function index()
    {
        $reasons = NotHappenReason::orderBy('label')->get();
        return response()->json(['data' => $reasons]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'          => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ur' => 'required|string',
            'status'         => 'sometimes|in:Active,Inactive',
        ]);

        $reason = NotHappenReason::create($data);
        return response()->json(['data' => $reason, 'message' => 'Reason created.'], 201);
    }

    public function show($id)
    {
        $reason = NotHappenReason::findOrFail($id);
        return response()->json(['data' => $reason]);
    }

    public function update(Request $request, $id)
    {
        $reason = NotHappenReason::findOrFail($id);

        $data = $request->validate([
            'label'          => 'sometimes|required|string|max:255',
            'description_en' => 'sometimes|required|string',
            'description_ur' => 'sometimes|required|string',
            'status'         => 'sometimes|in:Active,Inactive',
        ]);

        $reason->update($data);
        return response()->json(['data' => $reason, 'message' => 'Reason updated.']);
    }

    public function destroy($id)
    {
        $reason = NotHappenReason::findOrFail($id);
        $reason->delete();
        return response()->json(['message' => 'Reason deleted.']);
    }

    /** Public list — only Active, used by booking form and venue form */
    public function publicIndex()
    {
        $reasons = NotHappenReason::where('status', 'Active')->orderBy('label')->get();
        return response()->json(['data' => $reasons]);
    }
}
