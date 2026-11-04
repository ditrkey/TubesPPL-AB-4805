<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * 1. Menampilkan Halaman Peta (View)
     */
    public function index(): View
    {
        return view('map');
    }

}