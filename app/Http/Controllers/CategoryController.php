<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = session('categories', [
            ['id' => 1, 'name' => 'Seminar', 'description' => 'Acara seminar dan presentasi'],
            ['id' => 2, 'name' => 'Workshop', 'description' => 'Pelatihan praktis'],
            ['id' => 3, 'name' => 'Expo', 'description' => 'Pameran produk dan teknologi'],
        ]);
        
        return view('admin.categories.index', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories|min:3',
            'description' => 'nullable'
        ]);
        
        return redirect('/admin/categories')->with('success', 'Kategori berhasil ditambahkan!');
    }
    
    public function destroy($id)
    {
        return redirect('/admin/categories')->with('success', 'Kategori berhasil dihapus!');
    }
}