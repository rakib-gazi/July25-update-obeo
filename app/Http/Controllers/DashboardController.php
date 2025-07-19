<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use function Termwind\render;

class DashboardController extends Controller
{
    //
    function homePage(Request $request){
        return Inertia::render('HomePage');
    }
    function dashboard(Request $request){
        return Inertia::render('Dashboard');
    }
}
