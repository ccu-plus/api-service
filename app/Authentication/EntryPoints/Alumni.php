<?php

namespace App\Authentication\EntryPoints;

use App\Authentication\Validators\IdentityCardNumber;
use App\Authentication\Validators\Validator;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class Alumni extends EntryPoint
{
    /**
     * 身分證格式驗證.
     *
     * @return Validator
     */
    protected function validator(): Validator
    {
        return new IdentityCardNumber;
    }

    /**
     * 登入網址.
     *
     * @return string
     */
    protected function signInUrl(): string
    {
        return 'https://miswww1.ccu.edu.tw/alumni/alumni/login.php';
    }

    /**
     * 登入表單.
     *
     * @param string $username
     * @param string $password
     *
     * @return array<string>
     */
    protected function signInForm(string $username, string $password): array
    {
        return [
            'id' => $username,
            'password' => $password,
        ];
    }

    /**
     * 檢查是否登入成功.
     *
     * @param ResponseInterface $response
     *
     * @return bool
     */
    protected function signedIn(ResponseInterface $response): bool
    {
        $locations = $response->getHeader('location');

        if (!isset($locations[0])) {
            return false;
        }

        return false !== mb_strpos($locations[0], 'mainmenu.php');
    }

    /**
     * 登入完後處理.
     *
     * @param CookieJarInterface<CookieJar> $cookie
     *
     * @return bool
     */
    protected function postSignedIn(CookieJarInterface $cookie): bool
    {
        $url = 'https://miswww1.ccu.edu.tw/alumni/alumni/mainmenu.php';

        try {
            $response = $this->guzzle->request('GET', $url, [
                'allow_redirects' => false,
                'connect_timeout' => 2,
                'cookies' => $cookie,
                'timeout' => 3,
            ]);
        } catch (GuzzleException $e) {
            return false;
        }

        return $response->getStatusCode() === 200;
    }

    /**
     * 登出網址.
     *
     * @return string
     */
    protected function signOutUrl(): string
    {
        return 'https://miswww1.ccu.edu.tw/alumni/alumni/logout.php';
    }
}
