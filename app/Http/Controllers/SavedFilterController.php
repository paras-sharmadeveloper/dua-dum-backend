<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedFilter;

class SavedFilterController extends Controller
{
    // Get all saved filters for a page
    public function index(Request $request)
    {
        $filters = SavedFilter::where('user_id', auth()->id())
            ->where('page', $request->page)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $filters]);
    }

    // Save a new filter
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:50',
            'page'    => 'required|string',
            'filters' => 'required|array',
        ]);

        // Check duplicate name for same user + page
        $exists = SavedFilter::where('user_id', auth()->id())
            ->where('page', $request->page)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A filter with this name already exists.'
            ], 422);
        }

        // If set as default, remove default from others
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

        return response()->json([
            'message' => 'Filter saved successfully.',
            'data'    => $filter,
        ]);
    }

    // Set as default
    public function setDefault(SavedFilter $savedFilter)
    {
        // Remove default from others
        SavedFilter::where('user_id', auth()->id())
            ->where('page', $savedFilter->page)
            ->update(['is_default' => false]);

        $savedFilter->update(['is_default' => true]);

        return response()->json(['message' => 'Default filter set.']);
    }

    // Delete a filter
    public function destroy(SavedFilter $savedFilter)
    {
        if ($savedFilter->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $savedFilter->delete();

        return response()->json(['message' => 'Filter deleted.']);
    }
}
