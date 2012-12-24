<?php
/**
 * Steam Library
 * 
 * This library queries the Steam web API to retrieve information
 * about Steam users, games, news and stats. Depending on your 
 * server's proximity to the Steam Community servers in 
 * Washington, this library can take several seconds to execute.
 * Where possible, use AJAX instead of this library, as it will 
 * appear to load faster to the user.
 * This function needs the Steam API key variable to be set in
 *		/application/config/lanager.php
 *
 * @package		LANager
 * @category	Libraries
 * @author		ilumos <ilumos@gmail.com>
 * @link		http://www.zeropingheroes.co.uk/lanager/
 */
// ------------------------------------------------------------------------

class SteamWebApi {
	
	/**
	* Makes a Steam API request
	*
	* @param	str		$api_reuqest	API request, see http://steamcommunity.com/dev/
	* @param	str		$return_format	How the requested information is returned
	* 									Values: json, VDF, XML or array
	* @param	int		$steam_id		64 bit Steam ID(s) of user(s) for a request
	* 									Can be string or array, but some requests only
	* 									accept one ID
	* @param	int		$app_id			ID of game for a request 
	* @param	int		$news_count		Number of news items to return in a request
	* @param	int		$news_maxlen	Maximum length in characters for a news item
	* 									returned by a request
	* @return	The requested data on success, false on failure
	*/	
	function get_info($api_request, $steam_id, $app_id = NULL, $news_count = 3, $news_maxlen = 300)
	{

		if(is_array($steam_id))
		{
			$steam_id = implode(',',$steam_id); // turn array into comma separated list for request URL
			$multiple_steam_ids = TRUE;
		}
		else
		{
			$multiple_steam_ids = FALSE;
		}
		
		$api_key = Config::get('steamwebapi::config.api_key');
		$api_url = Config::get('steamwebapi::config.api_url');
		
		switch($api_request)
		{
			case 'GetPlayerSummaries':
				$request = 'ISteamUser/GetPlayerSummaries/v0002/?steamids='.$steam_id;
				break;
			case 'GetNewsForApp':
				$request = 'ISteamNews/GetNewsForApp/v0002/?appid='.$app_id.'&count='.$news_count.'&maxlength='.$news_maxlen;
				break;
			case 'GetFriendList':
				$request = 'ISteamUser/GetFriendList/v0001/?steamid='.$steam_id.'&relationship=friend';
				break;
			case 'GetGlobalAchievements':
				$request = 'ISteamUserStats/GetGlobalAchievementPercentagesForApp/v0002/?gameid='.$app_id;
				break;
			case 'GetPlayerAchievements':
				$request = 'ISteamUserStats/GetPlayerAchievements/v0001/?appid='.$appid.'&steamid='.$steam_id;
				break;
		}

		$request_url = $api_url.$request.'&format=json&key='.$api_key;
		
		// request the data
		$context = stream_context_create(array('http' => array('header' => 'Host: '.$_SERVER['SERVER_NAME'])));
		$response_data = file_get_contents($request_url, 0, $context);
		
		// if a response was received
		if($response_data != FALSE) 
		{
			// convert JSON to PHP array
			$response_array = json_decode($response_data,TRUE);
			if($api_request == 'GetPlayerSummaries' && $multiple_steam_ids == FALSE)
			{
				$response_array = $response_array['response']['players'][0]; // extract data from array
			}
			
			return $response_array;
		}
		else
		{
			return FALSE;
		}
	}
	// ------------------------------------------------------------------------
	
	
	function get_public_profile($steam_id = NULL)
	{
		if(!is_numeric($steam_id))
		{
			throw new Exception('Cannot get public profile information - invalid Steam ID provided');
		}
		
		$profile_xml = file_get_contents('http://steamcommunity.com/profiles/'.$steam_id.'/?xml=1&l=english');
		$xml = simplexml_load_string($profile_xml);
		
		foreach($xml->children() as $child)
		{
			$output[$child->getName()] = (string) $child; // cast from object to string
		}
		return $output;
	}
}