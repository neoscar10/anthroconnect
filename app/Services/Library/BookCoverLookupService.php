<?php

namespace App\Services\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class BookCoverLookupService
{
    /**
     * Normalize ISBN by removing spaces and hyphens.
     */
    public function normalizeIsbn(string $isbn): string
    {
        return strtoupper(preg_replace('/[^0-9X]/i', '', $isbn));
    }

    /**
     * Validate ISBN-10 or ISBN-13 checksum.
     */
    public function isValidIsbn(string $isbn): bool
    {
        $isbn = $this->normalizeIsbn($isbn);
        $len = strlen($isbn);

        if ($len === 10) {
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += (int)$isbn[$i] * (10 - $i);
            }
            $last = $isbn[9];
            $sum += ($last === 'X') ? 10 : (int)$last;
            return ($sum % 11 === 0);
        }

        if ($len === 13) {
            $sum = 0;
            for ($i = 0; $i < 13; $i++) {
                $sum += (int)$isbn[$i] * ($i % 2 === 0 ? 1 : 3);
            }
            return ($sum % 10 === 0);
        }

        return false;
    }

    /**
     * Fetch cover from Open Library and store it locally.
     */
    public function fetchAndStoreByIsbn(string $isbn, ?string $preferredName = null): array
    {
        $isbn = $this->normalizeIsbn($isbn);

        if (!$this->isValidIsbn($isbn)) {
            return [
                'success' => false,
                'message' => 'Enter a valid ISBN-10 or ISBN-13.'
            ];
        }

        // Priority: L (Large), then M (Medium), then S (Small)
        foreach (['L', 'M', 'S'] as $size) {
            $url = "https://covers.openlibrary.org/b/isbn/{$isbn}-{$size}.jpg?default=false";
            
            try {
                $response = Http::timeout(10)->get($url);

                if ($response->successful() && $response->header('Content-Type') && str_starts_with($response->header('Content-Type'), 'image/')) {
                    // It's a valid image
                    $filename = $this->generateFilename($isbn, $preferredName);
                    $path = "library/covers/{$filename}";
                    
                    Storage::disk('public')->put($path, $response->body());

                    return [
                        'success' => true,
                        'message' => 'Cover fetched successfully.',
                        'isbn' => $isbn,
                        'source_url' => $url,
                        'stored_path' => $path,
                        'public_url' => Storage::url($path)
                    ];
                }
            } catch (Exception $e) {
                // Continue to next size or fail
                continue;
            }
        }

        return [
            'success' => false,
            'message' => 'No cover was found for this ISBN.'
        ];
    }

    /**
     * Generate a sanitized filename for the cover.
     */
    protected function generateFilename(string $isbn, ?string $title = null): string
    {
        $prefix = $title ? Str::slug($title) : 'book';
        return "{$prefix}-{$isbn}-" . time() . '.jpg';
    }
}
