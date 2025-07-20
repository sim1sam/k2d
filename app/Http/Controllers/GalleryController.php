<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Redirect;
use Validator;

class GalleryController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_gallery'])->only('index');
        $this->middleware(['permission:add_gallery'])->only(['create', 'store']);
        $this->middleware(['permission:edit_gallery'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_gallery'])->only('destroy');

        // Validation Rules
        $this->gallery_rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'] // Validate images
        ];

        // Custom Validation Messages
        $this->gallery_messages = [
            'title.required' => translate('Gallery title is required'),
            'title.max' => translate('Gallery title must be within 255 characters'),
            'images.*.image' => translate('Each file must be an image'),
            'images.*.mimes' => translate('Only JPEG, PNG, JPG, and GIF files are allowed'),
            'images.*.max' => translate('Each image must be less than 2MB'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $galleries = Gallery::query();

        // Check if search keyword is provided
        if ($request->has('search')) {
            $sort_search = $request->search;
            $galleries = $galleries->where('title', 'like', '%' . $sort_search . '%')
                ->orWhere('description', 'like', '%' . $sort_search . '%');
        }

        // Order by latest and paginate results
        $galleries = $galleries->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.gallery.index', compact('galleries', 'sort_search'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Use validation rules and messages from the constructor
        $request->validate($this->gallery_rules, $this->gallery_messages);

        $gallery = new Gallery();
        $gallery->title = $request->title;
        $gallery->description = $request->description;

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                // Generate a unique filename
                $filename = time() . '_' . $image->getClientOriginalName();

                // Move the file to the public/gallery directory
                $image->move(public_path('gallery'), $filename);

                // Store the path in the images array
                $images[] = '' . $filename;
            }

            // Save the images in the gallery record without extra slashes
            $gallery->images = json_encode($images, JSON_UNESCAPED_SLASHES);
        }

        $gallery->save();

        return redirect()->route('backend.gallery.create')->with('success', 'Gallery added successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery)
    {
        return view('backend.gallery.show', compact('gallery'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gallery $gallery)
    {
        return view('backend.gallery.edit', compact('gallery'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request (reuse validation rules/messages)
        $request->validate($this->gallery_rules, $this->gallery_messages);

        // Find the gallery record
        $gallery = Gallery::findOrFail($id);

        // Update basic fields
        $gallery->title = $request->title;
        $gallery->description = $request->description;

        // Handle Image Uploads
        if ($request->hasFile('images')) {
            $images = [];

            // Delete old images (optional, if you want to remove old ones)
            if (!empty($gallery->images)) {
                foreach (json_decode($gallery->images) as $oldImage) {
                    $oldImagePath = public_path('gallery/' . $oldImage);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }

            // Upload new images
            foreach ($request->file('images') as $image) {
                // Generate unique filename
                $filename = time() . '_' . $image->getClientOriginalName();

                // Move the file to the gallery directory
                $image->move(public_path('gallery'), $filename);

                // Store the filename in the images array
                $images[] = $filename;
            }

            // Store the new images in the gallery record
            $gallery->images = json_encode($images, JSON_UNESCAPED_SLASHES);
        }

        $gallery->save();

        return redirect()->route('backend.gallery.edit', $id)->with('success', 'Gallery updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        // Delete associated images from storage
        if (!empty($gallery->images)) {
            foreach (json_decode($gallery->images) as $image) {
                $imagePath = public_path('storage/' . $image);
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the file
                }
            }
        }

        // Delete the gallery record from the database
        $gallery->delete();

        return redirect()->route('backend.gallery.index')->with('success', 'Gallery deleted successfully.');
    }

    public function Galleryindex()
    {
        $galleries = Gallery::all();

        // Ensure 'images' field is an array before passing to the view
        foreach ($galleries as $gallery) {
            $gallery->images = json_decode($gallery->images, true) ?? []; // Convert JSON string to array
        }

        return view('frontend.galleryindex', compact('galleries'));
    }

}
