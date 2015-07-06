@extends('layout.base')

@section('content')
    <h1>Twitter</h1>

    @foreach($tweets as $tweet)
        <div style="margin-bottom:10px;">
            ▪️{{$tweet}};
        </div>
    @endforeach
@stop
