<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');

        $categories = Category::query()
            ->withCount('songs')
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
            })
            ->when($filter === 'recent', function($query){
                $query->orderBy('created_at', 'desc');
            }, function($query){
                $query->ordered();
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'filter' => $filter
            ]
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 2 caracteres',
            'slug.required' => 'El slug es obligatorio',
            'slug.unique' => 'Este slug ya está en uso',
            'order.min' => 'El orden debe ser un número positivo'
        ]);

        $category = Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "Categoría '{$category->name}' creada exitosamente");
    }

    public function edit(Category $category): Response
    {
        $category->loadCount('songs');

        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 2 caracteres',
            'slug.required' => 'El slug es obligatorio',
            'slug.unique' => 'Este slug ya está en uso',
            'order.min' => 'El orden debe ser un número positivo'
        ]);

        $category->update($validated);

        $category->touch();

        return redirect()->route('admin.categories.index')
            ->with('success', "Categoría '{$category->name}' actualizada exitosamente");
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "🗑️ Categoría '{$name}' eliminada exitosamente");
    }
}
