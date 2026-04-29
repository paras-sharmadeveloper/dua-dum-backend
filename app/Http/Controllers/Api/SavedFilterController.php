<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\SavedFilter;

class SavedFilterController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $filters = SavedFilter::where('user_id', auth()->id())
            ->when($request->page_name, fn($q) => $q->where('page', $request->page_name))
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $filters]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'    => 'required|string|max:50',
            'page'    => 'required|string',
            'filters' => 'required|array',
        ]);

        $exists = SavedFilter::where('user_id', auth()->id())
            ->where('page', $request->page)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'A filter with this name already exists.'], 422);
        }

        if ($request->is_default) {
            SavedFilter::where('user_id', auth()->id())
                ->where('page', $request->page)
                ->update(['is_default' => false]);
        }

        $filter = SavedFilter::create([
            'user_id'    => auth()->id(),
            'name'       => $request->name,
            'page'       => $request->page,
            'filters'    => $request->filters,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json(['message' => 'Filter saved successfully.', 'data' => $filter], 201);
    }

    public function setDefault(SavedFilter $savedFilter): JsonResponse
    {
        if ($savedFilter->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        SavedFilter::where('user_id', auth()->id())
            ->where('page', $savedFilter->page)
            ->update(['is_default' => false]);

        $savedFilter->update(['is_default' => true]);

        return response()->json(['message' => 'Default filter set.']);
    }

    public function destroy(SavedFilter $savedFilter): JsonResponse
    {
        if ($savedFilter->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $savedFilter->delete();

        return response()->json(['message' => 'Filter deleted.']);
    }
}
