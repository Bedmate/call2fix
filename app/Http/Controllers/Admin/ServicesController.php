<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = trim($request->get('search', ''));

            $query = Service::with('category');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('service_name', 'LIKE', "%{$search}%")
                      ->orWhere('service_slug', 'LIKE', "%{$search}%");
                });
            }

            $services = $query->paginate(per_page());
            $categories = Category::all();

            return view('admin.services.index', compact('services', 'categories', 'search'));
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while fetching services.');
        }
    }

    // `create` method can be removed if you're using modals (optional)
    // But kept for completeness

    public function create(Request $request)
    {
        try {
            $categories = Category::all();
            return view('admin.services.create', compact('categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while loading the create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'service_name'  => 'required|string|max:255',
                'service_slug'  => 'nullable|string|max:255|unique:services,service_slug',
                'parent_service' => 'required|exists:categories,id', // matches form field name
            ]);

            // Auto-generate slug if not provided
            if (empty($validated['service_slug'])) {
                $validated['service_slug'] = Str::slug($validated['service_name']);
            }

            // Map form field to DB column
            $validated['category_id'] = $validated['parent_service'];
            unset($validated['parent_service']);

            $service = Service::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'service' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service: ' . $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Service $service)
    {
        try {
            $validated = $request->validate([
                'service_name'  => 'required|string|max:255',
                'service_slug'  => 'nullable|string|max:255|unique:services,service_slug,' . $service->id,
                'parent_service' => 'required|exists:categories,id',
            ]);

            if (empty($validated['service_slug'])) {
                $validated['service_slug'] = Str::slug($validated['service_name']);
            }

            $validated['category_id'] = $validated['parent_service'];
            unset($validated['parent_service']);

            $service->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'service' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage()
            ], 422);
        }
    }

    // Keep show/edit/destroy if used elsewhere, or remove if modals handle everything
}