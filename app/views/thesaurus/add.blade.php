@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('thesaurus.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Thesauru</h1>
                <form class="uk-form">
                    <div class="uk-form-file uk-text-primary">
                        text<input type="file">
                    </div>
                    <button class="uk-button">更新</button>
                </form>
            </div>
        </div>
    </div>
@endsection