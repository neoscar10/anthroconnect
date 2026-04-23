<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryResourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LibraryResourceTypeController extends Controller
{
    public function index()
    {
        $taxonomies = LibraryResourceType::orderBy('sort_order')->get();
        $title = 'Resource Types';
        $routePrefix = 'admin.library.resource-types';
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
        
        LibraryResourceType::create($validated);

        return redirect()->back()->with('success', 'Resource type created successfully.');
    }

    public function update(Request $request, LibraryResourceType $resourceType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        $resourceType->update($validated);

        return redirect()->back()->with('success', 'Resource type updated successfully.');
    }

    public function destroy(LibraryResourceType $resourceType)
    {
        if ($resourceType->resources()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete type that has associated resources.');
        }
        $resourceType->delete();
        return redirect()->back()->with('success', 'Resource type deleted.');
    }
}
