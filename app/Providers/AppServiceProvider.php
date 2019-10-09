<?php

namespace App\Providers;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->captcha();
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
