<?php

namespace App\Providers;

use App\Authentication\Authentication;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->redis();
    }

    /**
     * Setup redis driver.
     *
     * @return void
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
     *
     * @return void
     */
    public function register()
    {
        $this->captcha();

        $this->app->singleton('authentication', function () {
            return new Authentication;
        });
    }

    /**
     * Register captcha service.
     *
     * @return void
     */
    protected function captcha(): void
    {
        $this->app->singleton('captcha', function () {
            $phrase = new PhraseBuilder(5, '0123456789');

            $captcha = new CaptchaBuilder(null, $phrase);

            $captcha->setIgnoreAllEffects(true);

            $captcha->setMaxAngle(35);

            return $captcha;
        });
    }
}
