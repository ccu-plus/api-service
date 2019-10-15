<?php

namespace App\Http\Controllers;

use CCUPLUS\EloquentORM\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Thepixeldeveloper\Sitemap\Urlset;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;

class BaseController extends Controller
{
    public function index()
    {
        //
    }

    /**
     * 取得驗證碼.
     *
     * @return JsonResponse
     */
    public function captcha(): JsonResponse
    {
        $captcha = app('captcha')->build();

        $nonce = bin2hex(random_bytes(16));

        $captcha->getPhrase();

        Cache::put($nonce, $captcha->getPhrase(), 60 * 10);

        return response()->json([
            'data' => $captcha->inline(),
            'nonce' => $nonce,
        ]);
    }

    /**
     * Website sitemap.
     *
     * @return Response
     */
    public function sitemap(): Response
    {
        $urls = new Urlset;

        $base = 'https://ccu.plus';

        foreach (['', '/courses', '/sign-in'] as $page) {
            $urls->add(new Url(sprintf('%s%s', $base, $page)));
        }

        foreach (Course::all(['code'])->pluck('code')->toArray() as $code) {
            $urls->add(new Url(sprintf('%s/courses/%s', $base, $code)));
        }

        $urls->accept($xml = new XmlWriterDriver);

        return response($xml->output(), 200, [
            'content-type' => 'text/xml; charset=utf-8',
        ]);
    }
}
