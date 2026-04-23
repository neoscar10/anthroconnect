<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LibraryTagController extends Controller
{
    public function index()
    {
        $taxonomies = LibraryTag::latest()->get();
        $title = 'Tags';
        $routePrefix = 'admin.library.tags';
        return view('admin.library.taxonomies.index', compact('taxonomies', 'title', 'routePrefix'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        LibraryTag::create($validated);

        return redirect()->back()->with('success', 'Tag created successfully.');
    }

    public function update(Request $request, LibraryTag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $tag->update($validated);

        return redirect()->back()->with('success', 'Tag updated successfully.');
    }

    public function destroy(LibraryTag $tag)
    {
        $tag->delete();
        return redirect()->back()->with('success', 'Tag deleted.');
    }
}
