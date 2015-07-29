@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('review.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Review</h1><br>
                {{ Form::open(array('class'=>'uk-form uk-form-horizontal')) }}
                <fieldset data-uk-margin>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="item-name">登録名</label>
                        <div class="uk-form-controls">
                            {{ Form::text('item-name', '', array('id'=>'item-name')) }}
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="item-keyword">検索ワード</label>
                        <div class="uk-form-controls">
                            {{ Form::text('item-keyword', '', array('id'=>'item-keyword')) }}
                        </div>
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
