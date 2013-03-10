@layout('layouts/default')
@section('content')
<div class="profile_header">
	<img class="profile_avatar" src="{{$user->avatar_large}}">
	<span class="profile_username">{{e($user->username)}}</span>
	<ul class="profile_tools">
		@if($user->id == Session::get('user_id'))
			<li><a href="{{$steamProfile->getCommunityProfileLink()}}/edit" target="_blank">Edit Profile</a></li>
			<li><a href="{{URL::to_action('user@delete',array($user->id))}}">Delete Account</a></li>
		@else
			<li><a href="{{$steamProfile->getAddFriendLink()}}">Add</a></li>
			<li><a href="{{$steamProfile->getSendMessageLink()}}">Message</a></li>
			<li><a href="{{$steamProfile->getCommunityProfileLink()}}" target="_blank">View Steam Profile</a></li>
		@endif
	</ul>
</div>
<div class="profile_content">
	<div class="pull-right" style="text-align: right;">
	@if(is_numeric($steamProfile->getCurrentGameId()))
		Currently playing:
		<br>
		{{e($steamProfile->getCurrentGameName())}}
		<br>
		<a href="steam://store/{{$steamProfile->getCurrentGameId()}}" title="View in the Steam Store"><img src="http://cdn.steampowered.com/v/gfx/apps/{{$steamProfile->getCurrentGameId()}}/capsule_184x69.jpg"></a>
		<br>
		<a href="steam://connect/{{$steamProfile->getCurrentGameServerIP()}}" title="Connect to this server">{{$steamProfile->getCurrentGameServerIP()}}</a>
	@else
		{{e($steamProfile->getProfileStatus())}}
	@endif
	</div>
	<h3>Shouts</h3>
	@include('shout.list')
	<br>
</div>
@endsection