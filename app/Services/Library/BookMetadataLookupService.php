<?php

namespace App\Services\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BookMetadataLookupService
{
    protected $googleUrl = 'https://www.googleapis.com/books/v1/volumes';
    protected $openLibraryUrl = 'https://openlibrary.org/api/books';

    /**
     * Lookup book metadata by ISBN.
     */
    public function lookupByIsbn(string $isbn)
    {
        $normalizedIsbn = $this->normalizeIsbn($isbn);

        if (!$this->isValidIsbn($normalizedIsbn)) {
            return [
                'success' => false,
                'message' => 'Enter a valid ISBN-10 or ISBN-13.'
            ];
        }

        $errors = [];

        // Try Google Books First
        $googleResult = $this->lookupViaGoogle($normalizedIsbn);
        if ($googleResult['success']) return $googleResult;
        
        // If Google failed (quota or not found), log error but continue to Open Library
        if (isset($googleResult['message'])) {
            $errors[] = $googleResult['message'];
        }

        // If Google failed, try Open Library
        $olResult = $this->lookupViaOpenLibrary($normalizedIsbn);
        if ($olResult['success']) return $olResult;

        if (isset($olResult['message'])) {
            $errors[] = $olResult['message'];
        }

        // Determine the best error message to return
        $finalMessage = 'No book was found for this ISBN across our metadata providers.';
        
        if (in_array('Google Books API quota exceeded or unavailable.', $errors) && in_array('No book found on Open Library.', $errors)) {
            $finalMessage = 'Google Books quota exceeded and no record found on Open Library. Please try again later or enter details manually.';
        } elseif (!empty($errors)) {
            $finalMessage = $errors[count($errors) - 1]; // Return the last error (usually Open Library's "not found")
        }

        return [
            'success' => false,
            'message' => $finalMessage
        ];
    }

    /**
     * Google Books Lookup
     */
    protected function lookupViaGoogle(string $isbn)
    {
        try {
            $params = [
                'q' => 'isbn:' . $isbn,
            ];

            // Use API key if available to avoid quota issues
            $apiKey = config('services.google.books_api_key') ?: env('GOOGLE_BOOKS_API_KEY');
            if ($apiKey) {
                $params['key'] = $apiKey;
            }

            $response = Http::timeout(8)->get($this->googleUrl, $params);

            if ($response->failed()) {
                // If it's a 429 or 403, it's definitely a quota issue
                if ($response->status() === 429 || $response->status() === 403) {
                    return ['success' => false, 'message' => 'Google Books API quota exceeded or unavailable.'];
                }
                return ['success' => false, 'message' => 'Google Books service error (HTTP ' . $response->status() . ').'];
            }

            $data = $response->json();
            if (empty($data['items'])) {
                return ['success' => false, 'message' => 'No book found on Google Books.'];
            }

            $book = $data['items'][0]['volumeInfo'];
            return [
                'success' => true,
                'data' => $this->parseGoogleData($book, $isbn)
            ];
        } catch (\Exception $e) {
            Log::error('Google Books Lookup Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while connecting to Google Books.'];
        }
    }

    /**
     * Open Library Lookup
     */
    protected function lookupViaOpenLibrary(string $isbn)
    {
        try {
            $bibkey = 'ISBN:' . $isbn;
            $response = Http::timeout(8)->get($this->openLibraryUrl, [
                'bibkeys' => $bibkey,
                'format' => 'json',
                'jscmd' => 'data'
            ]);

            if ($response->failed()) {
                return ['success' => false, 'message' => 'Open Library API unavailable.'];
            }

            $data = $response->json();
            if (empty($data[$bibkey])) {
                return ['success' => false, 'message' => 'No book found on Open Library.'];
            }

            $book = $data[$bibkey];
            return [
                'success' => true,
                'data' => $this->parseOpenLibraryData($book, $isbn)
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Normalize ISBN.
     */
    protected function normalizeIsbn(string $isbn): string
    {
        return preg_replace('/[^0-9X]/i', '', $isbn);
    }

    /**
     * Validate ISBN.
     */
    protected function isValidIsbn(string $isbn): bool
    {
        $len = strlen($isbn);
        return ($len === 10 || $len === 13);
    }

    /**
     * Parse Google Books Data
     */
    protected function parseGoogleData(array $book, string $isbn): array
    {
        $imageLinks = $book['imageLinks'] ?? [];
        $coverUrl = $this->getBestGoogleImage($imageLinks);
        
        $storedCover = $coverUrl ? $this->downloadCover($coverUrl) : null;

        return [
            'normalized_isbn' => $isbn,
            'title' => $book['title'] ?? '',
            'authors' => isset($book['authors']) ? implode(', ', $book['authors']) : '',
            'publisher' => $book['publisher'] ?? '',
            'publication_year' => isset($book['publishedDate']) ? substr($book['publishedDate'], 0, 4) : '',
            'language' => $book['language'] ?? '',
            'page_count' => $book['pageCount'] ?? null,
            'categories' => $book['categories'] ?? [],
            'cover_source_url' => $coverUrl,
            'stored_cover_path' => $storedCover['path'] ?? null,
            'stored_cover_url' => $storedCover['url'] ?? null,
            'source' => 'Google Books'
        ];
    }

    /**
     * Parse Open Library Data
     */
    protected function parseOpenLibraryData(array $book, string $isbn): array
    {
        $authors = isset($book['authors']) ? collect($book['authors'])->pluck('name')->implode(', ') : '';
        $coverUrl = null;
        if (isset($book['cover'])) {
            $coverUrl = $book['cover']['large'] ?? $book['cover']['medium'] ?? $book['cover']['small'] ?? null;
        }

        $storedCover = $coverUrl ? $this->downloadCover($coverUrl) : null;

        return [
            'normalized_isbn' => $isbn,
            'title' => $book['title'] ?? '',
            'authors' => $authors,
            'publisher' => isset($book['publishers']) ? collect($book['publishers'])->pluck('name')->implode(', ') : '',
            'publication_year' => isset($book['publish_date']) ? substr(preg_replace('/[^0-9]/', '', $book['publish_date']), -4) : '',
            'language' => '', // OL data is inconsistent for language in 'data' jscmd
            'page_count' => $book['number_of_pages'] ?? null,
            'categories' => isset($book['subjects']) ? collect($book['subjects'])->pluck('name')->take(5)->toArray() : [],
            'cover_source_url' => $coverUrl,
            'stored_cover_path' => $storedCover['path'] ?? null,
            'stored_cover_url' => $storedCover['url'] ?? null,
            'source' => 'Open Library'
        ];
    }

    protected function getBestGoogleImage(array $links): ?string
    {
        $order = ['extraLarge', 'large', 'medium', 'small', 'thumbnail', 'smallThumbnail'];
        foreach ($order as $key) {
            if (!empty($links[$key])) {
                return str_replace('http://', 'https://', $links[$key]);
            }
        }
        return null;
    }

    protected function downloadCover(string $url): ?array
    {
        try {
            // Force HTTPS if possible
            $url = str_replace('http://', 'https://', $url);
            
            $response = Http::timeout(10)->get($url);
            if ($response->failed()) return null;

            $contentType = $response->header('Content-Type');
            if (!Str::startsWith($contentType, 'image/')) return null;

            $extension = 'jpg';
            if (Str::contains($contentType, 'png')) $extension = 'png';
            if (Str::contains($contentType, 'webp')) $extension = 'webp';

            $filename = 'isbn_' . time() . '_' . Str::random(10) . '.' . $extension;
            $path = 'library/covers/' . $filename;

            Storage::disk('public')->put($path, $response->body());

            return [
                'path' => $path,
                'url' => Storage::url($path),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
