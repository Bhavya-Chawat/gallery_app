<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class GalleryController extends Controller
{
    public function index()
    {
        return Inertia::render('Gallery/Index');
    }

    public function show($image)
    {
        return Inertia::render('Gallery/Show', [
            'image' => $image
        ]);
    }
}
