<?php

class Game_Controller extends Base_Controller {

	public function action_index()
	{
		$users = LANager\User::all();

		if(empty($users))
		{
			return View::make('game.popular')
						->with('title', 'Games')
						->with('error', 'No users in the database to check.');
		}

		// Collect all user IDs
		foreach ($users as $user)
		{
			$user_ids[] = $user->id;
		}

		$steamProfile = new SteamProfile($user_ids);

		$users = $steamProfile->fetchProfiles();

		// Only show users playing games
		$users = array_filter($users, function($user) {
			return array_key_exists('gameid',$user);
		});

		if(empty($users))
		{
			return View::make('game.popular')
						->with('title', 'Games')
						->with('error', 'Nobody\'s playing any games! What kind of LAN party is this!?');
		}

		// Make a list of all games being played
		foreach($users as $user)
		{
			$games_list[] = $user['gameid'].','.$user['gameextrainfo'];

		}

		// Tally the games being played
		$games = array_count_values($games_list);

		// Each game's id, name, user count and a list of users playing it
		foreach($games as $game => $user_count)
		{
			$game = explode(',', $game);
			$game['id'] = $game[0];
			$game['name'] = $game[1];
			unset($game[0]);
			unset($game[1]);
			
			foreach($users as $user)
			{
				if($user['gameid'] == $game['id'])
				{
					$top_games[$game['id']]['name'] = $game['name'];
					$top_games[$game['id']]['app_id'] = $game['id'];
					$top_games[$game['id']]['user_count'] = $user_count;
					$top_games[$game['id']]['users'][] = array('id' => $user['steamid'], 'username' => $user['personaname']);
				}
			}
		}

		// Sort by popularity, in decending order
		usort($top_games, function($a, $b) {
			return $b['user_count'] - $a['user_count'];
		});

		return View::make('game.popular')
					->with('title', 'Games')
					->with('games', $top_games);
	}

}