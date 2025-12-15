<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Announcement::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort_by field to prevent SQL injection
        $allowedSortFields = ['id', 'title', 'created_at', 'updated_at', 'publish_at', 'is_active'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min(max((int)$perPage, 1), 100); // Limit between 1 and 100

        $announcements = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $announcements->items(),
            'pagination' => [
                'current_page' => $announcements->currentPage(),
                'per_page' => $announcements->perPage(),
                'total' => $announcements->total(),
                'last_page' => $announcements->lastPage(),
                'from' => $announcements->firstItem(),
                'to' => $announcements->lastItem(),
            ]
        ], 200);
    }

    /**
     * Store a newly created announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
            'publish_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        // Create announcement with image_path
        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'publish_at' => $validated['publish_at'] ?? null,
            'is_active' => $validated['is_active'] ?? true, // Default to true if not provided (though validate might require it if not 'sometimes') but migration has default
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully',
            'data' => $announcement
        ], 201);
    }

    /**
     * Display the specified announcement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $announcement
        ], 200);
    }

    /**
     * Update the specified announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'publish_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            // Store new image
            $imagePath = $request->file('image')->store('announcements', 'public');
            $announcement->image_path = $imagePath;
        }

        // Update other fields
        if (isset($validated['title'])) $announcement->title = $validated['title'];
        if (isset($validated['content'])) $announcement->content = $validated['content'];
        if (array_key_exists('publish_at', $validated)) $announcement->publish_at = $validated['publish_at'];
        if (isset($validated['is_active'])) $announcement->is_active = $validated['is_active'];

        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully',
            'data' => $announcement
        ], 200);
    }

    /**
     * Remove the specified announcement from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        // Delete image if exists
        if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully'
        ], 200);
    }
}
