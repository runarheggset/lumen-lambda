<?php

namespace Runar1\Lambda;

use Illuminate\Support\ServiceProvider;

class LambdaServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 */
	public function boot() {

	}

	/**
	 * Register the service provider.
	 */
	public function register() {
		if (env('AWS_LAMBDA') === true) {
			$this->app->configureMonologUsing(function($monolog) {
				$syslog = new \Monolog\Handler\SyslogHandler('lumen');
				$formatter = new \Monolog\Formatter\LineFormatter(null, null, false, true);
				$syslog->setFormatter($formatter);
				$monolog->pushHandler($syslog);
				return $monolog;
			});
		}
	}
}
