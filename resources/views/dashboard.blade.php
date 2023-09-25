@extends('root::app')

{{-- Title --}}
@section('title', 'Dashboard')

{{-- Content --}}
@section('content')
    <div class="l-row l-row--column:sm:2 l-row--column:lg:3">
        @foreach($widgets as $widget)
            {!! $widget !!}
        @endforeach
    </div>
@endsection