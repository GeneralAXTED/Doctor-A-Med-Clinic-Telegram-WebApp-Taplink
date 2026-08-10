<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctoraController extends Controller
{
    /**
     * Display the Doctor-A Med Clinic Taplink / WebApp page
     */
    public function index()
    {
        return view('doctora');
    }
}
