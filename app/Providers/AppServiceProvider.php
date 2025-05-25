<?php

declare(strict_types=1);

namespace App\Providers;

use App\Authentication\Authentication;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Boot the authentication services for the application.
     */
    public function boot(): void
    {
        $this->redis();
    }

    /**
     * Setup redis driver.
     */
    protected function redis(): void
    {
        if (class_exists(\Redis::class)) {
            return;
        }

        $this->app->config->set('database.redis.client', 'predis');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->captcha();

        $this->app->singleton('authentication', function (): \App\Authentication\Authentication {
            return new Authentication;
        });
    }

    /**
     * Register captcha service.
     */
    protected function captcha(): void
    {
        $this->app->singleton('captcha', function (): \Gregwar\Captcha\CaptchaBuilder {
            $phrase = new PhraseBuilder(5, '0123456789');

            $captcha = new CaptchaBuilder(null, $phrase);

            $captcha->setIgnoreAllEffects(true);

            $captcha->setMaxAngle(35);

            return $captcha;
        });
    }
}
