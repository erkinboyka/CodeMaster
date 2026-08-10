<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    protected $pages = [
        'about' => 'About Us',
        'terms' => 'Terms of Service',
        'privacy' => 'Privacy Policy',
        'contacts' => 'Contacts',
    ];

    public function show($page)
    {
        if (!array_key_exists($page, $this->pages)) {
            abort(404);
        }

        return view('static.' . $page, ['title' => $this->pages[$page]]);
    }
}
