@layout('layouts/default')
@section('content')
<div class="profile_header">
	<img class="profile_avatar" src="{{$user->avatar_large}}">
	<span class="profile_username">{{$user->username}}</span>
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
	<span class="pull-right">{{$steamProfile->getProfileStatus()}}</span>
	<h3>Shouts</h3>
	@include('shout.list')
</div>
@endsection