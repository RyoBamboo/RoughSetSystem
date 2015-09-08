@extends('base')

@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('graph.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
            </div>
        </div>
    </div>
    @include('graph.make_modal')
@endsection