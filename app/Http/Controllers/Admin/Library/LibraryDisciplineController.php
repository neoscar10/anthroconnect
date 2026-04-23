<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryDiscipline;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LibraryDisciplineController extends Controller
{
    public function index()
    {
        $taxonomies = LibraryDiscipline::orderBy('sort_order')->get();
        $title = 'Disciplines';
        $routePrefix = 'admin.library.disciplines';
        return view('admin.library.taxonomies.index', compact('taxonomies', 'title', 'routePrefix'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        LibraryDiscipline::create($validated);

        return redirect()->back()->with('success', 'Discipline created successfully.');
    }

    public function update(Request $request, LibraryDiscipline $discipline)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        $discipline->update($validated);

        return redirect()->back()->with('success', 'Discipline updated successfully.');
    }

    public function destroy(LibraryDiscipline $discipline)
    {
        if ($discipline->resources()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete discipline that has associated resources.');
        }
        $discipline->delete();
        return redirect()->back()->with('success', 'Discipline deleted.');
    }
}
