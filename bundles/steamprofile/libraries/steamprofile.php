<?php

class SteamProfile {

	private $steamId64;

	private $profileName;

	private $profileCreated;

	private $profileStatus;

	private $realName;

	private $lastLogOff;

	private $profileUrl;

	private $primaryGroupId;

	private $registrationDate;

	private $avatarSmall;

	private $avatarMedium;

	private $avatarLarge;

	private $gameName;

	private $gameId;

	private $gameServerIp;


	public function __construct($steamId64)
	{
		$this->steamId64 = $steamId64;
	}

	public function fetchFriends()
	{
		// 
	}

	public function fetchProfile()
	{

		$apiKey = Config::get('steamprofile::config.api_key');
		$apiUrl = Config::get('steamprofile::config.api_url');

		$requestUrl = $apiUrl.'ISteamUser/GetPlayerSummaries/v0002/?steamids='.$this->steamId64.'&format=json&key='.$apiKey;

		$context = stream_context_create(array('http' => array('header' => 'Host: '.$_SERVER['SERVER_NAME'])));
		$responseData = file_get_contents($requestUrl, 0, $context);
		$responseArray = json_decode($responseData,TRUE);
		
		$profile = $responseArray['response']['players'][0];

		$this->profileName = $profile['personaname'];

		$this->profileCreated = array_key_exists('profilestate', $profile) ? $profile['profilestate'] : null;

		$this->profileStatus = $profile['personastate'];

		$this->lastLogOff = $profile['lastlogoff'];

		$this->profileUrl = $profile['profileurl'];

		$this->avatarSmall = $profile['avatar'];

		$this->avatarMedium = $profile['avatarmedium'];

		$this->avatarLarge = $profile['avatarfull'];

		// Private data

		$this->primaryGroupId = array_key_exists('primaryclanid', $profile) ? $profile['primaryclanid'] : null;

		$this->registrationDate = array_key_exists('timecreated', $profile) ? $profile['timecreated'] : null;

		$this->realName = array_key_exists('realname', $profile) ? $profile['realname'] : null;

		$this->gameName = array_key_exists('gameextrainfo', $profile) ? $profile['gameextrainfo'] : null;

		$this->gameId = array_key_exists('gameid', $profile) ? $profile['gameid'] : null;

		$this->gameServerIp = array_key_exists('gameserverip', $profile) ? $profile['gameserverip'] : null;

	}

	public function getSteamId64()
	{
		return $this->steamId64;
	}

	public function getProfileName()
	{
		return $this->profileName;
	}

	public function getAvatarSmall()
	{
		return $this->avatarSmall;
	}

	public function getAvatarMedium()
	{
		return $this->avatarMedium;
	}

	public function getAvatarLarge()
	{
		return $this->avatarLarge;
	}

	public function getCurrentGameName()
	{
		return $this->gameName;
	}

	public function getCurrentGameId()
	{
		return $this->gameId;
	}

	public function getCurrentGameServerIp()
	{
		return $this->gameServerIp;
	}

	public function isOnline()
	{
		return ($this->profileStatus != 0);
	}

	public function getProfileStatus()
	{
		switch($this->profileStatus)
		{
			case 0: return 'Last seen '.DateFmt::Format('AGO[ymodhms]', $this->lastLogOff);
			case 1: return 'Online now';
			case 2: return 'Online now, busy';
			case 3: return 'Online now, away';
			case 4: return 'Online now, idle';
			case 5: return 'Online now, looking to trade';
			case 5: return 'Online now, looking to play';
		}
	}

	public function getAddFriendLink()
	{
		return 'steam://friends/add/'.$this->steamId64;
	}

	public function getSendMessageLink()
	{
		return 'steam://friends/joinchat/'.$this->steamId64;
	}

	public function getCommunityProfileLink()
	{
		return 'http://www.steamcommunity.com/profiles/'.$this->steamId64;
	}

}