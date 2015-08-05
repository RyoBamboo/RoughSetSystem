@extends('base')

@section('css')
    <link rel="stylesheet" type="text/css" href={{asset("/assets/css/uikit/css/components/progress.gradient.css")}}>
@endsection


@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('thesaurus.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Thesauru</h1>
                <form class="uk-form">
                    <select>
                        <option>文字を入力して登録する</option>
                        <option>CSVファイルを入力して登録する</option>
                    </select>

                    <div class="tm-upload-button">
                        ファイルを選択
                        <input type="file">
                    </div>
                    <div class="uk-form-file uk-text-primary">
                        text<input type="file">
                    </div>
                    <button class="uk-button">更新</button>
                </form>
            </div>
        </div>
    </div>
@endsection