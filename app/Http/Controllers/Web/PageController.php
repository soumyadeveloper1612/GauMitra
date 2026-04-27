<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function termsAndConditions()
    {
        return view('web.terms-and-conditions');
    }
}