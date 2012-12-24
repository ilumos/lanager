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

		// Sign-in button (TODO: cache link)
		$login_button = 'https://steamcommunity.com/openid/login?openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth'
						.'%2F2.0&openid.mode=checkid_setup&openid.return_to='.urlencode(URL::base().'user/login')
						.'&openid.realm='.urlencode(URL::base()).'&openid.ns.sreg=http%3A%2F%2Fopenid.net%2Fextensions'
						.'%2Fsreg%2F1.1&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select'
						.'&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select';

		View::share('login_button', $login_button);
	}

}