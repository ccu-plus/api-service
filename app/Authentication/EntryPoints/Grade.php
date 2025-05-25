<?php

declare(strict_types=1);

namespace App\Authentication\EntryPoints;

use App\Authentication\Validators\StudentId;
use App\Authentication\Validators\Validator;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use Psr\Http\Message\ResponseInterface;

class Grade extends EntryPoint
{
    /**
     * 學號格式驗證.
     */
    protected function validator(): Validator
    {
        return new StudentId;
    }

    /**
     * 登入網址.
     */
    protected function signInUrl(): string
    {
        return 'https://kiki.ccu.edu.tw/~ccmisp06/cgi-bin/Query/Query_grade.php';
    }

    /**
     * 登入表單.
     *
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
     *
     */
    protected function signedIn(ResponseInterface $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $content = $response->getBody()->getContents();

        return false !== mb_strpos($content, '國立中正大學');
    }

    /**
     * 登入完後處理.
     *
     * @param CookieJarInterface<CookieJar> $cookie
     */
    protected function postSignedIn(CookieJarInterface $cookie): bool
    {
        return true;
    }

    /**
     * 登出網址.
     */
    protected function signOutUrl(): string
    {
        return 'https://kiki.ccu.edu.tw/~ccmisp06/cgi-bin/Query/index.html';
    }
}
