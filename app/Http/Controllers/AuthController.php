<?php

namespace App\Http\Controllers;

use App\Http\Validators\SignInValidator;
use App\Transformers\FormValidationTransformer;
use App\Transformers\SignInTransformer;
use CCUPLUS\EloquentORM\User;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $v = SignInValidator::make($request);

        if ($v->fails()) {
            return fractal($v->errors(), new FormValidationTransformer)->respond(422);
        }

        $input = $v->validated();

        $cookie = app('authentication')->signIn($input['username'], $input['password'], $input['type']);

        if (false === $cookie) {
            throw new UnauthorizedHttpException('Incorrect username or password.');
        } else if ($input['type'] === 'alumni') {
            $input['username'] = $this->alumniUsername($cookie);

            if (is_null($input['username'])) {
                throw new BadRequestHttpException;
            }
        }

        return fractal(User::query()->where('username', '=', $input['username'])->first())
            ->transformWith(new SignInTransformer)
            ->respond();
    }

    /**
     * 校友取得學號.
     *
     * @param CookieJar $cookie
     *
     * @return string|null
     */
    protected function alumniUsername(CookieJar $cookie): ?string
    {
        $url = 'https://miswww1.ccu.edu.tw/alumni/alumni/updateContact.php';

        $response = (new Client)->get($url, ['cookies' => $cookie]);

        $content = $response->getBody()->getContents();

        if (0 === preg_match('/學號.+(\d{9})/suU', $content, $matches)) {
            return null;
        }

        return $matches[1];
    }

    public function signUp()
    {
        //
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
