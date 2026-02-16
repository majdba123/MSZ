<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * List all categories.
     */
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $query = Category::query()->with('subcategories');

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $categories = $query->latest()->get();

        return response()->json([
            'message' => __('Categories retrieved successfully.'),
            'data' => $categories,
        ]);
    }

    /**
     * Show a specific category.
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('subcategories');

        return response()->json([
            'message' => __('Category retrieved successfully.'),
            'data' => $category,
        ]);
    }

    /**
     * Create a new category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('categories', 'public');
        }

        $category = Category::create($data);
        $category->load('subcategories');

        return response()->json([
            'message' => __('Category created successfully.'),
            'data' => $category,
        ], 201);
    }

    /**
     * Update an existing category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($category->logo) {
                Storage::disk('public')->delete($category->logo);
            }
            $data['logo'] = $request->file('logo')->store('categories', 'public');
        } else {
            // Remove logo from data if not being updated
            unset($data['logo']);
        }

        $category->update($data);
        $category->load('subcategories');

        return response()->json([
            'message' => __('Category updated successfully.'),
            'data' => $category,
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): JsonResponse
    {
        // Delete logo if exists
        if ($category->logo) {
            Storage::disk('public')->delete($category->logo);
        }

        $category->delete();

        return response()->json([
            'message' => __('Category deleted successfully.'),
        ]);
    }
}
