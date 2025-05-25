<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;

class SitemapController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $base = 'https://ccu.plus';

        $urls = new Urlset;

        foreach (['', '/courses'] as $page) {
            $urls->add(new Url(sprintf('%s%s', $base, $page)));
        }

        foreach (Course::pluck('code')->toArray() as $code) {
            $urls->add(new Url(sprintf('%s/courses/%s', $base, $code)));
        }

        $urls->accept($xml = new XmlWriterDriver);

        return response($xml->output(), 200, [
            'Content-Type' => 'text/xml; charset=utf-8',
        ]);
    }
}
