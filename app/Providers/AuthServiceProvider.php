<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Http\Request;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Boot the authentication services for the application.
     */
    public function boot(): void
    {
        /** @var AuthManager $auth */
        $auth = $this->app['auth'];

        $auth->viaRequest('api', function (Request $request) {
            $token = $this->apiTokenToUserToken($request->header('api-token'));

            if ($token === false) {
                return null;
            }

            return User::query()
                ->where('token', '=', $token)
                ->first();
        });
    }

    /**
     * Verify api token and retrieve token from it.
     *
     * @param  mixed  $apiKey
     * @return bool|string
     */
    protected function apiTokenToUserToken($apiKey)
    {
        if (empty($apiKey) || ! is_string($apiKey)) { // 確認 apiKey 為字串
            return false;
        }

        if (strpos($apiKey, '.') === false) { // 確認 apiKey 含有「.」
            return false;
        }

        [$token, $hmac] = explode('.', $apiKey);

        if (empty($token) || empty($hmac)) { // 確認 token 和 hmac 不為空
            return false;
        }

        if (false === ($hmac = base64_decode($hmac, true))) { // 確認 hmac 能正確 base64 decode
            return false;
        }

        $known = hash_hmac('md5', $token, env('APP_KEY'), true);

        if (hash_equals($known, $hmac) === false) { // 確認 hash mac 一致
            return false;
        }

        return $token;
    }
}
