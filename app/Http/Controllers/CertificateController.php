<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function show($hash)
    {
        $certificate = Certificate::with(['user', 'course'])
            ->where('cert_hash', $hash)
            ->firstOrFail();

        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('certificates.show', compact('certificate'));
    }

    public function download($hash)
    {
        $certificate = Certificate::with(['user', 'course'])
            ->where('cert_hash', $hash)
            ->firstOrFail();

        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $html = view('certificates.print', compact('certificate'))->render();

        $filename = "certificate-{$certificate->cert_hash}.html";

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
    }
}
