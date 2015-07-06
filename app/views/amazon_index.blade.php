@extends('layout.base')

@section('content')
    <h1>Amazon</h1>

    @foreach($reviews as $review)
        <div style="margin-bottom:10px;">
            ▪️{{$review}};<br><br>
        </div>
    @endforeach
@stop
