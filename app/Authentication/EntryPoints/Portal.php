<?php

namespace App\Authentication\EntryPoints;

use App\Authentication\Validators\StudentId;
use App\Authentication\Validators\Validator;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use Psr\Http\Message\ResponseInterface;

class Portal extends EntryPoint
{
    /**
     * 學號格式驗證.
     *
     * @return Validator
     */
    protected function validator(): Validator
    {
        return new StudentId;
    }

    /**
     * 登入網址.
     *
     * @return string
     */
    protected function signInUrl(): string
    {
        return 'https://portal.ccu.edu.tw/login_check.php';
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
            'acc' => $username,
            'pass' => $password,
            'authcode' => '請輸入右邊文字',
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

        return false !== mb_strpos($locations[0], 'sso_index.php');
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
        return true;
    }

    /**
     * 登出網址.
     *
     * @return string
     */
    protected function signOutUrl(): string
    {
        return 'https://portal.ccu.edu.tw/logout_check.php';
    }
}
