<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageStoreRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function store(ImageStoreRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('items', 'public');
                $validatedData['image_url'] = Storage::url($imagePath);
            }
            return response()->json(['image_url' => $validatedData['image_url'] ?? null], 201);
        } catch (Exception $error) {
            if (isset($imagePath)) Storage::disk('public')->delete($imagePath);
            Log::error('Error creating item: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to create item',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
