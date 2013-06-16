@layout('layouts/default')
@section('content')

<h3>Events</h3>

<?php

if(!empty($events))
{
	// calculate bounds of timetable

	// floor earliest event's start time to hour
	$timetable['start'] = strtotime(date('Y/m/d H', $timetable['first_event_start']).':00'); 

	// floor latest event's end time to hour
	$timetable['end'] = strtotime(date('Y/m/d H', $timetable['last_event_end']).':00');
	
	// get the hour the timetable must start on
	$timetable['start_hour'] = date('H',$timetable['start']);

	// calculate the total number of hours the timetable will be rendering
	$timetable['total_hours'] = ceil(abs($timetable['start'] - $timetable['end']) / 3600);

	// calculate number of rows required to render the entire timetable
	$timetable['total_rows'] = ($timetable['total_hours'] * 3600) / Config::get('lanager.timetable_time_per_row');



?>

<table id="timetable">
<tbody>

<?php

$i = 0;

foreach($events as $event)
{
	$events_array_temp[$i] = $event->to_array();

	$events_array[strtotime($events_array_temp[$i]['start'])] = $events_array_temp[$i];

	$i++;

}

$rows_spanned = 0;

// create timetable, one row at a time
for ($i = 0; $i <= $timetable['total_rows']; $i++)
{
	// calculate the time marking we're at by adding (1 row's time * loop number) to the start time
	$timetable['current_row_time'] = $timetable['start'] + ($i * Config::get('lanager.timetable_time_per_row'));

	// if the current row time is on an hour
	if(($timetable['current_row_time'] % 3600) == 0)
	{
		// generate a label for the top row
		$timetable['row_time_label'] = date('ga',$timetable['current_row_time']);
	}
	else
	{
		$timetable['row_time_label'] = '&nbsp;';
	}

	// set a flag if midnight has been passed
	$timetable['row_new_day'] = (($timetable['current_row_time'] % 86400) == 0) ? ' class="day"' : NULL;

	echo '
	<tr>
		<th>'.$timetable['row_time_label'].'</th>';

		// if there is an event at this time
		if(array_key_exists($timetable['current_row_time'], $events_array))
		{
			// calculate rows required for the event's length
			$rows_spanned = (strtotime($events_array[$timetable['current_row_time']]['end'])-$timetable['current_row_time'])/Config::get('lanager.timetable_time_per_row');

			echo '<td class="event_name" rowspan="'.$rows_spanned.'">'.$events_array[$timetable['current_row_time']]['name'].'</td>';
			echo '<td class="event_description" rowspan="'.$rows_spanned.'">'.$events_array[$timetable['current_row_time']]['description'].'</td>';
		}

		// if empty tds need to be inserted (no rows spanning over the space)
		if($rows_spanned == 0) {
			echo '
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			';
		}

	echo '</tr>';
	
	// decrement the rows spanned variable as we have filled a row
	if($rows_spanned > 0) {
		$rows_spanned--;
	}

}
?>

</tbody>
</table>

<?php

}
else
{
	echo 'No events to show!';
}

?>
@endsection