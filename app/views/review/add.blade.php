@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('review.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Review</h1><br>
                {{ Form::open(array('class'=>'uk-form')) }}
                <fieldset data-uk-margin>
                    <div class="uk-form-row">
                        {{ Form::label('item-name', '登録名') }}
                        {{ Form::text('item-name') }}
                    </div>
                    <div class="uk-form-row">
                        {{ Form::label('search-type', '検索方法') }}
                        {{ Form::select('search-type', array('item-keyword'=>'キーワードから検索', 'item-code'=>'レビューコードから検索'), 'item-keyword', array('id'=>'search-type')) }}
                    </div>
                    <div class="uk-form-row">
                        {{ Form::text('item-keyword', '', array('id'=>'item-keyword')) }}
                    </div>
                </fieldset>
                {{ Form::close() }}
                {{ Form::submit('検索', array('id'=>'item-search', 'class'=>'uk-button uk-button-primary')) }}
                {{ Form::submit('登録', array('id'=>'get-review', 'class'=>'uk-button uk-button-primary')) }}
                <table id="resultTable" class="uk-table">
                </table>
            </div>
        </div>
    </div>
    {{-- include modal --}}
    @include('review.add_modal')
@endsection
