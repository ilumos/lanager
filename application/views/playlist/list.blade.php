<table id="playlist">
	<tbody>
	@forelse ($playlist_entries->results as $entry)
		<tr class="playlist_entry{{ ($entry->playback_state == 1 OR $entry->playback_state == 2) ? ' entry_now_playing' : ''}}">
			<td class="playlist_entry_user">
				<a href="{{URL::to_action('user@profile',$entry->user->id)}}"><img src="{{$entry->user->avatar_small}}" title="{{ e($entry->user->username) }}"></a>
			</td>
			<td class="playlist_entry_duration">
				@if($entry->duration >= 3600)
					{{ e(intval(gmdate('H',$entry->duration)).'h '.intval(gmdate('i',$entry->duration)).'m '.intval(gmdate('s',$entry->duration)).'s') }}
				@elseif($entry->duration >= 60)
					{{ e(intval(gmdate('i',$entry->duration)).'m '.intval(gmdate('s',$entry->duration)) .'s') }}
				@else
					{{ e(intval(gmdate('s',$entry->duration)) .'s') }}
				@endif
			</td>
			<td class="playlist_entry_title">
				{{ e($entry->title) }}
			</td>
			<td class="playlist_entry_controls">
				@if(Authority::can('control', 'playlist_playback'))
					@if($entry->playback_state == 1)
						<a href="{{URL::to_action('playlist@pause')}}" class="btn btn-success" title="Pause playback"><i class="icon-pause"></i></a>
					@elseif($entry->playback_state == 2)
						<a href="{{URL::to_action('playlist@play')}}" class="btn btn-success" title="Resume playback"><i class="icon-play"></i></a>
					@endif
				@endif
				@if(Authority::can('delete', 'playlist_entry', $entry))
					<a href="{{URL::to_action('playlist@delete_entry',$entry->id)}}" class="btn btn-danger" title="Delete this entry"><i class="icon-trash"></i></a>
				@endif
				@if(Authority::can('vote_skip', 'playlist_entry', $entry) && $entry->playback_state == 1)
					<a href="{{URL::to_action('playlist@vote_skip_entry',$entry->id)}}" class="btn btn-warning" title="Vote to skip this entry"><i class="icon-step-forward"></i></a>
				@endif
			</td>
		</tr>	
	@empty
		<tr colspan="3">No entries to show!</tr>
	@endforelse
	</tbody>
</table>

{{$playlist_entries->links()}}