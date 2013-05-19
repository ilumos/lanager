@layout('layouts/default')
@section('content')

<h2>Files</h2>

@forelse($locations as $location)
		@if(parse_url($location->location,PHP_URL_SCHEME) == 'http')
			<a href="#" rel="tooltip" class="dotted-underline" data-toggle="tooltip" data-placement="right"
				title="Slow. No way to download whole folders">Web:</a>

			<a href="{{e($location->location)}}" target="_blank">{{e($location->location)}}</a><br>
		
		@elseif(parse_url($location->location,PHP_URL_SCHEME) == 'ftp')
			<a href="#" rel="tooltip" class="dotted-underline" data-toggle="tooltip" data-placement="right"
				title="Fast. FTP client needed download whole folders">FTP:</a>

			<a href="{{e($location->location)}}" target="_blank">{{e($location->location)}}</a><br>
		
		@elseif(strpos($location->location,'\\') !== false)
			<a href="#" rel="tooltip" class="dotted-underline" data-toggle="tooltip" data-placement="right"
				title="Fast. Can download whole folders. Paste into Start > Run">Windows:</a>

			<a href="{{e($location->location)}}" target="_blank">{{e($location->location)}}</a><br>
		
		@else
			<a href="#" rel="tooltip" class="dotted-underline" data-toggle="tooltip" data-placement="right"
				title="Ask an admin how to connect to this location">Unknown:</a>

			<a href="{{e($location->location)}}" target="_blank">{{e($location->location)}}</a><br>
		@endif
		{{e($location->description)}}
		<br>
		<br>
@empty
	<p>No files are available to download - go pester an admin!</p>
@endforelse

<p>Don't forget to check the <a href="{{URL::to_action('info@index')}}">Info</a> section for install and gameplay guides!</p>

@endsection