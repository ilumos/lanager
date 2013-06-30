<table id="playlist">
	<tbody>
	@forelse ($playlist_entries->results as $entry)
		<tr class="playlist_entry{{ ($entry->playback_state == 1) ? ' entry_now_playing' : ''}}">
			<td class="playlist_entry_user">
				<a href="{{URL::to_action('user@profile',$entry->user->id)}}"><img src="{{$entry->user->avatar_small}}" title="{{ e($entry->user->username) }}"></a>
			</td>
			<td class="playlist_entry_duration">
				{{ e(intval(gmdate('i',$entry->duration)).':'.gmdate('s',$entry->duration)) }}
			</td>
			<td class="playlist_entry_title">
				{{ e($entry->title) }}
			</td>
			<td class="playlist_entry_controls">
				@if(Config::get('lanager.playlist_screen_allowed_user') == Session::get('user_id'))
					<a href="{{URL::to_action('playlist@delete_entry',$entry->id)}}">X</a>
				@endif
			</td>
		</tr>	
	@empty
		<tr colspan="3">No entries to show!</tr>
	@endforelse
	</tbody>
</table>


{{$playlist_entries->links()}}