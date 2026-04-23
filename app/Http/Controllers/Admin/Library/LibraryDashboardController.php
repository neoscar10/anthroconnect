<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Services\Library\LibraryAdminService;

class LibraryDashboardController extends Controller
{
    protected $libraryService;

    public function __construct(LibraryAdminService $libraryService)
    {
        $this->libraryService = $libraryService;
    }

    public function index()
    {
        $stats = $this->libraryService->getDashboardStats();
        return view('admin.library.dashboard', compact('stats'));
    }
}
