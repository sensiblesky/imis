<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class FileUploadService
{
    public static function upload(UploadedFile $file, string $type, string $oldFile = null): string
{
    // Define allowed mime types based on $type or allow all supported ones
    $validator = Validator::make(['file' => $file], [
        'file' => 'required|file|mimes:jpeg,jpg,png,gif,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        throw new \Exception("Invalid file type or size. Only image or PDF files up to 2MB are allowed.");
    }

    // If it's an image, further validate using getimagesize
    if (in_array($file->extension(), ['jpeg', 'jpg', 'png', 'gif'])) {
        if (getimagesize($file) === false) {
            throw new \Exception("The file is not a valid image.");
        }
    }

    // Delete old file if exists
    if ($oldFile && Storage::disk('public')->exists($oldFile)) {
        Storage::disk('public')->delete($oldFile);
    }

    // Define folder path
    $folder = match ($type) {
        'profile_photo'        => 'uploads/photos/users',
        'identity_replacement' => 'uploads/documents',
        'excel'                => 'uploads/excel',
        'assignment'           => 'uploads/assignments',
        default                => 'uploads/others',
    };

    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

    return $file->storeAs($folder, $filename, 'public');
}


}
