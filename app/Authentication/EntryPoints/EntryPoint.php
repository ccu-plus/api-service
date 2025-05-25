<?php

namespace App\Authentication\EntryPoints;

use App\Authentication\Validators\Validator;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;

abstract class EntryPoint
{
    /**
     * Guzzle Http Client instance.
     *
     * @var ClientInterface
     */
    protected $guzzle;

    /**
     * Constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->guzzle = $client;
    }

    /**
     * 登入服務.
     *
     * @param string $username
     * @param string $password
     *
     * @return CookieJarInterface<CookieJar>|false
     *
     * @throws GuzzleException
     */
    public function signIn(string $username, string $password)
    {
        if (!$this->validator()->valid($username)) {
            return false;
        }

        $response = $this->guzzle->request('POST', $this->signInUrl(), [
            'allow_redirects' => false,
            'connect_timeout' => 5,
            'cookies' => $cookie = new CookieJar,
            'form_params' => $this->signInForm($username, $password),
            'timeout' => 5,
        ]);

        if (!$this->signedIn($response) || !$this->postSignedIn($cookie)) {
            return false;
        }

        return $cookie;
    }

    /**
     * 登出.
     *
     * @param CookieJarInterface<CookieJar> $cookie
     *
     * @return bool
     *
     * @throws GuzzleException
     */
    public function signOut(CookieJarInterface $cookie): bool
    {
        try {
            $this->guzzle->request('GET', $this->signOutUrl(), [
                'connect_timeout' => 1,
                'cookies' => $cookie,
                'timeout' => 2,
            ]);
        } catch (TransferException $e) {
            return false;
        }

        return true;
    }

    /**
     * 帳號格式驗證.
     *
     * @return Validator
     */
    abstract protected function validator(): Validator;

    /**
     * 登入網址.
     *
     * @return string
     */
    abstract protected function signInUrl(): string;

    /**
     * 登入表單.
     *
     * @param string $username
     * @param string $password
     *
     * @return array<string>
     */
    abstract protected function signInForm(string $username, string $password): array;

    /**
     * 檢查是否登入成功.
     *
     * @param ResponseInterface $response
     *
     * @return bool
     */
    abstract protected function signedIn(ResponseInterface $response): bool;

    /**
     * 登入完後處理.
     *
     * @param CookieJarInterface<CookieJar> $cookie
     *
     * @return bool
     */
    abstract protected function postSignedIn(CookieJarInterface $cookie): bool;

    /**
     * 登出網址.
     *
     * @return string
     */
    abstract protected function signOutUrl(): string;
}
