<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\ImageUpload;

class ImageController extends Controller
{
    public function fetch(Request $request)
    {
        $sortField = $request->query('sort_field', 'uploaded_at');

        $sortOrder = $request->query('sort_order', 'desc');

        $images = ImageUpload::orderBy($sortField, $sortOrder)->get();

        return view('welcome', compact('images'));
    }

    public function fetchAsJson() {
        $images = ImageUpload::all();

        return response()->json($images);
    }

    public function fetchAsJsonById(int $id) {
        $image = ImageUpload::find($id);

        if (!$image) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        return response()->json($image);
    }

    public function upload(Request $request)
    {
        if (count($request->file('images')) > 5) {
            abort(422, 'Maximum of 5 images allowed');
        }

        $request->validate([
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        foreach ($request->file('images') as $image) {
            $originalName = $image->getClientOriginalName();

            $title = basename($originalName, '.'. $image->getClientOriginalExtension());

            $transliteratedTitle = \Transliterator::create('Any-Latin; Latin-ASCII')->transliterate($title);

            $uniqueId = Str::random(10);

            $filename = $transliteratedTitle . '_'. $uniqueId. '.'. $image->getClientOriginalExtension();

            $image->move(public_path('images'), $filename);

            ImageUpload::create([
                'filename' => $filename,
                'uploaded_at' => now(),
            ]);
        }

        return back()->with('success', 'Images uploaded successfully.');
    }

    public function downloadAsZip(int $id) {
        $image = ImageUpload::find($id);

        if (!$image) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        $zip = new ZipArchive();
        $fileName = 'image.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === true) {
            $filePath = public_path("images/{$image->filename}");
            $zip->addFile($filePath, basename($filePath));
            $zip->close();

            return response()->file(public_path($fileName));
        } else {
            return response()->json(['error' => 'Failed to create ZIP file'], 500);
        }
    }
}
