<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Member::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|array', // Menerima array
            'role.*' => 'nullable|string|max:255', // Setiap item di role harus string
            'task' => 'nullable|array', // Menerima array
            'task.*' => 'nullable|string', // Setiap item di task harus string
            'image' => 'nullable|url|max:2048',
            'github' => 'nullable|url|max:2048',
        ]);

        // Pastikan role dan task adalah array, jika null dari input, set ke array kosong
        $validated['role'] = $validated['role'] ?? [];
        $validated['task'] = $validated['task'] ?? [];

        $member = Member::create($validated);
        return response()->json($member, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        return response()->json($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'role' => 'nullable|array', // Menerima array
            'role.*' => 'nullable|string|max:255',
            'task' => 'nullable|array', // Menerima array
            'task.*' => 'nullable|string',
            'image' => 'nullable|url|max:2048',
            'github' => 'nullable|url|max:2048',
        ]);

        // Pastikan role dan task adalah array, jika null dari input, set ke array kosong
        $validated['role'] = $validated['role'] ?? [];
        $validated['task'] = $validated['task'] ?? [];

        $member->update($validated);
        return response()->json($member);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return response()->json(null, 204);
    }
}
