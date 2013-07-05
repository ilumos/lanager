<?php

class Seed_Database {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		LANager\Role::create(array(	'name' => 'admin'));
		LANager\Role::create(array(	'name' => 'attendee'));
		LANager\Role::create(array(	'name' => 'playlist_screener'));
		
		$user = LANager\User::create(array(	'username' => '[ZPH] ilumos', 'steam_id_64' => '76561197970613738', 'avatar' => 'http://media.steampowered.com/steamcommunity/public/images/avatars/3e/3e837f842aa97b3d16f313ec7908d103b55d205e.jpg'));

		$shout = new LANager\Shout(array('content' => 'Welcome to the LANager!'));
		$shout = $user->shouts()->insert($shout);

		$playlist_entry = new LANager\Playlist_entry(array('video_id' => 'OpCJzdWxEbQ', 'title' => 'Clean The Fan', 'duration' => 251));
		$playlist_entry = $user->playlist_entries()->insert($playlist_entry);

		LANager\Info::create(array(	'title' => 'The LANager',
									'content' => "### What the hell is this? ###\r\n\r\nSo, the LANager is a super useful website available during the LAN that lets you do some nifty things:\r\n\r\n-   Find out [what we've got planned][7]\r\n\r\n[7]: <http://lanager/events/timetable>\r\n\r\n-   Add music and videos to the collaborative [playlist][1]\r\n\r\n[1]: <http://lanager/playlist>\r\n\r\n-   [Shout][2] out what you want to play\r\n\r\n[2]: <http://lanager/shouts>\r\n\r\n-   [Chat][3] in the local chatroom\r\n\r\n[3]: <http://lanager:7778/>\r\n\r\n-   [Download][4] Games\r\n\r\n[4]: <http://lanager/files>\r\n\r\n-   See [who's here][5] at the LAN\r\n\r\n[5]: <http://lanager/people>\r\n\r\n-   Read [info][6] about the event and how to install/play certain games\r\n\r\n[6]: <http://lanager/info>\r\n\r\n-   See [who's playing what][8]\r\n\r\n[8]: <http://lanager/games/>\r\n\r\n\r\n\r\n\r\nSo, sign in through Steam above and try it out!"
							));

		LANager\File_location::create(array('location' => 'ftp://files/'));
		LANager\File_location::create(array('location' => '\\\\files\\'));
		LANager\File_location::create(array('location' => 'http://files/'));


	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('roles')->where_in('name', array('admin', 'attendee', 'playlist_screener'))->delete();
		DB::table('users')->where('steam_id_64','=','76561197970613738')->delete();
		DB::table('shouts')->where('content','=','Welcome to the LANager!')->delete();
		DB::table('playlist_entries')->where('video_id','=','OpCJzdWxEbQ')->delete();


		DB::query('TRUNCATE TABLE info');
		DB::query('TRUNCATE TABLE file_locations');
	}

}