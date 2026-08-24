<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Vacancy;
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

        $data = ['title' => $this->pages[$page]];

        if ($page === 'about') {
            $data['stats'] = [
                'students' => User::count(),
                'courses' => Course::count(),
                'countries' => User::where('country_name', '!=', '')->distinct('country_name')->count('country_name'),
                'vacancies' => Vacancy::count(),
            ];
        }

        return view('static.' . $page, $data);
    }
}
