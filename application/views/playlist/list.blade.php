<table id="playlist">
	<tbody>
	@forelse ($playlist_entries->results as $entry)
		<tr class="playlist_entry{{ ($entry->playback_state == (1 OR 2)) ? ' entry_now_playing' : ''}}">
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
					@if($entry->playback_state == 1)
						<a href="{{URL::to_action('playlist@pause')}}" class="btn btn-success" title="Pause playback"><i class="icon-pause"></i></a>
					@elseif($entry->playback_state == 2)
						<a href="{{URL::to_action('playlist@play')}}" class="btn btn-success" title="Resume playback"><i class="icon-play"></i></a>
					@endif
					<a href="{{URL::to_action('playlist@skip_entry',$entry->id)}}" class="btn btn-warning" title="Skip this entry"><i class="icon-step-forward"></i></a>
					<a href="{{URL::to_action('playlist@delete_entry',$entry->id)}}" class="btn btn-danger" title="Delete this entry"><i class="icon-trash"></i></a>
				@else
					<a href="#voteskip" class="btn btn-warning" title="Vote to skip this entry"><i class="icon-step-forward"></i></a>
				@endif
			</td>
		</tr>	
	@empty
		<tr colspan="3">No entries to show!</tr>
	@endforelse
	</tbody>
</table>


{{$playlist_entries->links()}}