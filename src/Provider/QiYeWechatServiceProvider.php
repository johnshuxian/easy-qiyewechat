<?php

namespace John\QiYeWechat\Provider;

use Illuminate\Support\ServiceProvider;
use John\QiYeWechat\QiYeWechat;

class QiYeWechatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('QiYeWechat', function () {
            return new QiYeWechat();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/config.php' => config_path('qy_wechat.php')]);
    }
}
