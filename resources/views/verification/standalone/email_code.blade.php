@extends('layouts.popup')

@section('content')
	<div class="alert {{ $type }}">
		{{ $message }}
	</div>
@endsection
