<?php

class Base_Controller extends Controller {

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

	public function __construct()
	{
		// Assets
		Asset::add('jquery', 'js/jquery-1.8.2.min.js');
		Asset::add('modernizr', 'js/vendor/modernizr-2.6.2-respond-1.1.0.min.js');
		Asset::add('bootstrap-js', 'js/vendor/bootstrap.min.js');
		Asset::add('main', 'js/main.js');

		Asset::add('bootstrap', 'css/bootstrap.css');
		Asset::add('bootstrap-responsive', 'css/bootstrap-responsive.min.css');
		Asset::add('style', 'css/main.css');
		parent::__construct();

		// Sign-in button
		Bundle::start('LightOpenID');
		$openID = new LightOpenID;
		$openID->identity = 'http://steamcommunity.com/openid';
		$openID->returnUrl = URL::to_route('login');
		$login_button = Cache::remember('login_button', function() use ($openID) {return $openID->authUrl();}, 60*24);

		View::share('login_button', $login_button);
	}

}