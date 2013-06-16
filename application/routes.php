<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post(array('hello', 'world'), function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/

// Route::get('/', function()
// {
// 	return View::make('home.index');
// });


// User ----------------------------------------------
Route::get('login',
	array(
		'as'=>'login',
		'uses'=>'user@login',
	));
Route::get('logout',
	array(
		'as'=>'logout',
		'uses'=>'user@logout',
	));
Route::get('preferences',
	array(
		'as'=>'preferences',
		'uses'=>'user@preferences',
	));
Route::get('people',
	array('as' => 'people',
		'uses'=>'user@list',
	));

Route::get('profile/(:num)',
	array(
		'as' => 'profile',
		'uses'=>'user@profile',
	));

// Shouts --------------------------------------------
Route::get('shouts',
	array(
		'as' => 'shouts',
		'uses'=>'shout@index',
	));
Route::post('shout/post',
	array(
		'before' => 'csrf|logged_in',
		'uses'=>'shout@post',
	));

// Events --------------------------------------------
Route::get('events',
	array(
		'as' => 'events',
		'uses'=>'event@index',
	));
Route::get('events/create',
	array(
		'as' => 'events',
		'uses'=>'event@get_create',
	));
Route::post('events/create',
	array(
		'before' => 'csrf',
		'uses'=>'event@post_create',
	));
// Games ---------------------------------------------
Route::get('games',
	array(
		'as' => 'games',
		'uses'=>'game@index',
	));

// Info ---------------------------------------------
Route::get('info',
	array(
		'as' => 'info',
		'uses'=>'info@index',
	));

Route::get('info/display/(:num)',
	array(
		'as' => 'info',
		'uses'=>'info@display',
	));

// Files ---------------------------------------------
Route::get('files',
	array(
		'as' => 'files',
		'uses'=>'files@index',
	));

// Playlist ------------------------------------------
Route::get('playlist',
	array(
		'as' => 'playlist',
		'uses'=>'playlist@index',
	));
Route::post('playlist/add_entry', array(
		'before' => 'csrf|logged_in',
		'as' => 'playlist',
		'uses'=>'playlist@add_entry',
	));
Route::get('playlist/history', array(
		'as' => 'playlist',
		'uses'=>'playlist@history',
	));

Route::get('playlist/screen',
	array(
		'before' => 'auth_playlist_screen',
		'as' => 'playlist', 
		'uses'=>'playlist@screen',
	));
Route::get('playlist/get_entry', array(
		'before' => 'auth_playlist_screen',
		'as' => 'playlist',
		'uses'=>'playlist@get_entry',
	));

Route::get('playlist/mark_entry/(:all)/(:num)', array(
		'before' => 'auth_playlist_screen',
		'as' => 'playlist',
		'uses'=>'playlist@mark_entry',
	));

Route::get('playlist/pause', array(
		'before' => 'auth_playlist_screen',
		'as' => 'playlist',
		'uses'=>'playlist@pause',
	));

Route::get('playlist/play', array(
		'before' => 'auth_playlist_screen',
		'as' => 'playlist',
		'uses'=>'playlist@play',
	));

Route::get('playlist/skip', array(
		'before' => 'auth_playlist_screen',
		'as' => 'playlist',
		'uses'=>'playlist@skip',
	));
Route::get('playlist/delete/(:all)', array(
		'before' => 'auth_playlist_screen',
		'as' => 'playlist',
		'uses'=>'playlist@delete_entry',
	));

// Index
Route::get('/', 'shout@index');

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('logged_in', function()
{
	if( !Session::has('username') ) return Response::error('403');
});

// Only allow the defined user to view the playlist screen
Route::filter('auth_playlist_screen',function()
{
	if(Config::get('lanager.playlist_screen_allowed_user') != Session::get('user_id')) return Redirect::to('playlist');
});









/*
|--------------------------------------------------------------------------
| View Composers
|--------------------------------------------------------------------------
*/



// Info dropdown, to show top level info pages
View::composer(array('partials.info'), function($view)
{
    $view->with('children', LANager\Info::where_null('parent_id')->get());

});