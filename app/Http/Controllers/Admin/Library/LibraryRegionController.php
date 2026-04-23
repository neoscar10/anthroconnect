<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryRegion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LibraryRegionController extends Controller
{
    public function index()
    {
        $taxonomies = LibraryRegion::orderBy('sort_order')->get();
        $title = 'Regions';
        $routePrefix = 'admin.library.regions';
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
        
        LibraryRegion::create($validated);

        return redirect()->back()->with('success', 'Region created successfully.');
    }

    public function update(Request $request, LibraryRegion $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        $region->update($validated);

        return redirect()->back()->with('success', 'Region updated successfully.');
    }

    public function destroy(LibraryRegion $region)
    {
        if ($region->resources()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete region that has associated resources.');
        }
        $region->delete();
        return redirect()->back()->with('success', 'Region deleted.');
    }
}
