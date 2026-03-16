<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{

    public function index()
    {
        return view('events');
    }

    public function detail($id)
    {
        return view('event_detail', compact('id'));
    }

}