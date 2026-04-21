<?php

namespace App\Http\Controllers;

use App\Services\WorkingLadyService;
use App\Services\HelperServices\DatatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkingLadyController extends Controller
{
    protected $workingLadyService;
    protected $dataTableService;

    public function __construct(
        WorkingLadyService $workingLadyService,
        DatatableService $dataTableService
    ) {
        $this->workingLadyService = $workingLadyService;
        $this->dataTableService = $dataTableService;
    }

    /**
     * Display a listing of working ladies
     */
    public function index()
    {
        return view('working-lady.index');
    }

    /**
     * Get working ladies data for DataTable
     */
    public function getData(Request $request)
    {
        return $this->workingLadyService->getWorkingLadiesData($request, $this->dataTableService);
    }

    /**
     * Show the form for creating a new working lady
     */
    public function create()
    {
        return view('working-lady.create');
    }

    /**
     * Store a newly created working lady
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'place_of_work' => 'required|string|max:255',
            'email' => 'required|email|unique:working_ladies,email',
            'phone_number' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'case_type' => 'required|in:normal,critical',
            'status' => 'nullable|string|in:Approved,Disapproved,Pending',
        ]);

        $result = $this->workingLadyService->create($validated);

        if ($result['success']) {
            return redirect()->route('working-lady.index')->with('success', $result['message']);
        }

        return redirect()->back()->withErrors(['error' => $result['message']])->withInput();
    }

    /**
     * Display the specified working lady for editing
     */
    public function edit(string $id)
    {
        $result = $this->workingLadyService->find($id);

        if (!$result['success']) {
            return redirect()->route('working-lady.index')->withErrors(['error' => $result['message']]);
        }

        return view('working-lady.edit', ['workingLady' => $result['data']]);
    }

    /**
     * Update the specified working lady
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'place_of_work' => 'required|string|max:255',
            'email' => 'required|email|unique:working_ladies,email,' . $id,
            'phone_number' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'case_type' => 'required|in:normal,critical',
            'status' => 'nullable|string|in:Approved,Disapproved,',
        ]);

        $result = $this->workingLadyService->update($id, $validated);

        if ($result['success']) {
            return redirect()->route('working-lady.index')->with('success', $result['message']);
        }

        return redirect()->back()->withErrors(['error' => $result['message']])->withInput();
    }

    /**
     * Remove the specified working lady
     */
    public function destroy(string $id)
    {
        $result = $this->workingLadyService->delete($id);

        if ($result['success']) {
            return response()->json(['message' => $result['message']]);
        }

        return response()->json(['message' => $result['message']], 500);
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Approved,Disapproved,',
        ]);

        $result = $this->workingLadyService->updateStatus($id, $validated['status']);

        if ($result['success']) {
            return response()->json(['message' => $result['message']]);
        }

        return response()->json(['message' => $result['message']], 500);
    }
}
