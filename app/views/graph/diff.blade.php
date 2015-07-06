@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('graph.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article">Graph</h1><br>
                {{ Form::open(array('url'=>'/graph/testView', 'class'=>'uk-form' , 'method'=>'get')) }}
                <fieldset data-uk-margin>
                    <div class="uk-form-row">
                        {{ Form::select('item1', $item_names, 1)}}
                        <span>と</span>
                        {{ Form::select('item2', $item_names, 2)}}
                        <span>の差分グラフを確認する</span>
                    </div>
                </fieldset>
                {{ Form::submit('決定', array('class'=>'uk-button uk-button-primary')) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection