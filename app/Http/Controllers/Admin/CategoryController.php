<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $categories = Category::orderBy('name')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }
    
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        $validated = $request->validate([
            'name' => 'required|min:3|unique:categories',
            'description' => 'nullable|string'
        ]);
        
        Category::create($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }
    
    public function update(Request $request, Category $category)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        $validated = $request->validate([
            'name' => 'required|min:3|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);
        
        $category->update($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diupdate!');
    }
    
    public function destroy(Category $category)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        if ($category->events()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki event!');
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}