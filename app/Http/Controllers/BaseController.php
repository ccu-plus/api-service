<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;

class BaseController extends Controller
{
    /**
     * 取得驗證碼.
     *
     *
     * @throws Exception
     */
    public function captcha(): JsonResponse
    {
        $captcha = app('captcha')->build();

        $nonce = bin2hex(random_bytes(16));

        Cache::put($nonce, $captcha->getPhrase(), 60 * 10);

        return response()->json([
            'data' => $captcha->inline(),
            'nonce' => $nonce,
        ]);
    }

    /**
     * Matomo analytics.
     */
    public function push(Request $request): JsonResponse
    {
        $response = response()->json([], 204);

        if (! in_array($action = $request->input('type'), ['pageview', 'search'])) {
            return $response;
        }

        (new Client)->post('https://matomo.ccu.plus/piwik.php', [
            'http_errors' => false,
            'form_params' => [
                'idsite' => 1,
                'rec' => 1,
                'action_name' => $action,
                'url' => $request->input('url'),
                '_id' => $request->input('uid'),
                'apiv' => 1,
                'ua' => $request->input('agent'),
                'uid' => optional($request->user())->getKey(),
                'cip' => $request->ip(),
                'token_auth' => env('MATOMO_SECRET'),
                'search' => $action === 'search' ? $request->input('search') : null,
                'search_count' => $action === 'search' ? $request->input('count') : null,
            ],
        ]);

        return $response;
    }

    /**
     * Website sitemap.
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
