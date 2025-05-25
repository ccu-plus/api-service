<?php

declare(strict_types=1);

namespace App\Authentication;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use InvalidArgumentException;

class Authentication
{
    /**
     * Entry point namespace.
     *
     * @var string
     */
    const NAMESPACE = 'App\Authentication\EntryPoints\\';

    /**
     * 保存 target 值之 cookie 名稱.
     *
     * @var string
     */
    const TARGET = '_target_';

    /**
     * 驗證用戶登入.
     *
     *
     * @return CookieJarInterface<CookieJar>|false
     */
    public function signIn(string $username, string $password, string $target)
    {
        $class = sprintf(
            '%s%s',
            self::NAMESPACE,
            $target = ucfirst(mb_strtolower($target))
        );

        if (! class_exists($class)) {
            throw new InvalidArgumentException(
                sprintf('%s is not a valid entry point.', $target)
            );
        }

        /** @var CookieJarInterface<CookieJar>|false $cookie */
        $cookie = (new $class(new Client))->signIn($username, $password);

        if ($cookie === false) {
            return false;
        }

        $cookie->setCookie($this->targetCookie($target));

        return $cookie;
    }

    /**
     * 設置 target cookie.
     */
    protected function targetCookie(string $target): SetCookie
    {
        return new SetCookie([
            'Name' => self::TARGET,
            'Value' => $target,
            'Domain' => '0',
        ]);
    }

    /**
     * 登出用戶.
     *
     * @param  CookieJar<CookieJar>  $jar
     */
    public function signOut(CookieJar $jar): bool
    {
        $cookie = $jar->getCookieByName(self::TARGET);

        if (is_null($cookie)) {
            return false;
        }

        $target = $cookie->getValue();

        $class = sprintf('%s%s', self::NAMESPACE, $target);

        return (new $class(new Client))->signOut($jar);
    }
}
