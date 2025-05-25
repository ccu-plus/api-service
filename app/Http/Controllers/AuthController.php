<?php

namespace App\Http\Controllers;

use App\Http\Validators\SignInValidator;
use App\Http\Validators\SignUpValidator;
use App\Transformers\AuthTransformer;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    /**
     * 登入.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function signIn(Request $request): JsonResponse
    {
        $input = SignInValidator::make($request);

        $cookie = app('authentication')->signIn($input['username'], $input['password'], $input['type']);

        if (false === $cookie) {
            throw new UnauthorizedHttpException('Incorrect username or password.');
        } else if ($input['type'] === 'alumni' && is_null($input['username'] = $this->username($cookie))) {
            throw new BadRequestHttpException;
        }

        return fractal(User::query()->firstOrNew(['username' => $input['username']]))
            ->transformWith(new AuthTransformer)
            ->respond();
    }

    /**
     * 校友取得學號.
     *
     * @param CookieJar $cookie
     *
     * @return string|null
     */
    protected function username(CookieJar $cookie): ?string
    {
        $url = 'https://miswww1.ccu.edu.tw/alumni/alumni/updateContact.php';

        $response = (new Client)->get($url, ['cookies' => $cookie]);

        $content = $response->getBody()->getContents();

        if (0 === preg_match('/學號.+(\d{9})/suU', $content, $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * 註冊.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function signUp(Request $request): JsonResponse
    {
        $input = SignUpValidator::make($request);

        $token = decrypt($input['token']);

        if (abs($token['timestamp'] - time()) > 600) {
            throw new AccessDeniedHttpException;
        }

        $user = User::query()->create([
            'username' => $token['username'],
            'nickname' => $input['nickname'],
            'email' => $input['email'],
            'token' => Str::random(16),
        ]);

        return fractal($user)
            ->transformWith(new AuthTransformer)
            ->respond();
    }

    /**
     * 登出.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function signOut(Request $request): JsonResponse
    {
        $request->user()->update([
            'token' => Str::random(16),
        ]);

        return response()->json([], 204);
    }
}
