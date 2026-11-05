<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class ImageStore
{
    public function store(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, $this->disk());
    }

    public function replace(?string $current, ?UploadedFile $file, string $folder): ?string
    {
        if ($file === null) {
            return $current;
        }

        $path = $this->store($file, $folder);
        $this->delete($current);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path !== null) {
            Storage::disk($this->disk())->delete($path);
        }
    }

    public static function rules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:'.implode(',', config('appeal.uploads.mimes')),
            'max:'.config('appeal.uploads.max_kilobytes'),
            'dimensions:max_width=6000,max_height=6000',
        ];
    }

    private function disk(): string
    {
        return config('appeal.uploads.disk');
    }
}
