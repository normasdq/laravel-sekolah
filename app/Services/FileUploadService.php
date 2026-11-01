<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    private const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/jpg', 'image/webp',
    ];

    public function upload(UploadedFile $file, string $directory = 'uploads', ?int $maxWidth = null): string
    {
        $this->validateFile($file);

        $filename = $this->generateFilename($file);
        $path = $file->storeAs($directory, $filename, 'public');

        return $path;
    }

    public function delete(string $path): bool
    {
        if (empty($path) || ! Storage::disk('public')->exists($path)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    private function validateFile(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException('Format file tidak diizinkan. Gunakan format JPEG, PNG, atau WebP.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException('Ukuran file maksimal 5MB.');
        }
    }

    private function generateFilename(UploadedFile $file): string
    {
        return Str::random(16).'_'.time().'.'.$file->getClientOriginalExtension();
    }
}
