@layout('layouts/default')
@section('content')

<h3>Events</h3>

<?php
// calculate bounds of timetable
$timetable['start'] = strtotime(date('Y/m/d H', $timetable['first_event_start']).':00'); // floor earliest event's start time to hour
$timetable['end'] = strtotime(date('Y/m/d H', $timetable['last_event_end']).':00')+3600; // floor latest event's end time to hour & add 1 hour

// get the hour the timetable must start on
$timetable['start_hour'] = date('H',$timetable['start']);

$timetable['total_hours'] = ceil(abs($timetable['start'] - $timetable['end']) / 3600);

// calculate number of 15 min blocks required
$timetable['total_blocks'] = ($timetable['total_hours'] * 4) + 3; // add 3 for last hour's blocks

?>

<div class="timetableTableContainer">
	<table cellpadding="0" cellspacing="0" border="0" class="timetableTable">
	<tbody>
	<tr>
		<td id="timetableLabel">

@for ($i = 0; $i <= $timetable['total_hours']; $i++)
			<div class="hourCell">
				<p>{{(($i+$timetable['start_hour'])%24)}}</p>
			</div>
			<div class="minCell rowALabel">
				:00
			</div>
			<div class="minCell">
				:15
			</div>
			<div class="minCell rowALabel">
				:30
			</div>
			<div class="minCell">
				:45
			</div>
@endfor
		</td>

		<td>
			<div>
@for ($i = 0; $i <= $timetable['total_blocks']; $i++)
				@if ($i % 2 == 0 OR $i == 0)
					<div class="timetableGuide rowA">
						&nbsp;
					</div>
				@else
					<div class="timetableGuide">
						&nbsp;
					</div>
				@endif
@endfor
			</div>
		</td>

<?php
foreach ($events as $event)
{
		$hour_start = date('g:i a',strtotime($event->start));
		$hour_end = date('g:i a',strtotime($event->end));

		$shrink = 8;

		$top = 22 * ((strtotime($event->start) - $timetable['start']) / 900) + ($shrink/2);
		$height = (22 *((strtotime($event->end) - strtotime($event->start)) / 900))-20 - $shrink ;

		echo '<div class="event" style="top:'.$top.'px; height:'.$height.'px;"> 
			<div class="eventHeader">
				&nbsp;'.$hour_start.' - '.$hour_end.'
			</div>
			 <h4>'.$event->title.'</h4>
			 <br>
		</div>';
	}
?>

	</tr>
	</tbody>
	</table>
</div>

@endsection