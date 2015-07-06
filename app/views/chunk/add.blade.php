@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('chunk.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Chunk</h1><br>
                {{ Form::open(array('url'=>url('/chunk/store'), 'method'=>'post', 'class'=>'uk-form')) }}
                    <fieldset data-uk-margin>
                        <div class="uk-form-row">
                            {{ Form::label('from', 'from :') }}
                            {{ Form::text('from', Input::old('from')) }}
                            {{ Form::label('to', 'to :') }}
                            {{ Form::text('to', Input::old('to')) }}
                        </div>
                        <div class="uk-form-row">
                            {{ Form::label('nega_posi', 'type :') }}
                            {{ Form::select('nega_posi', array('p'=>'p', 'n'=>'n', 'f'=>'f', 'non'=>'non'), Input::old('nega_posi')) }}
                        </div>
                        <div class="uk-form-row">
                            <button type='submit' class="uk-button uk-button-primary">新規登録</button>
                        </div>
                    </fieldset>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
