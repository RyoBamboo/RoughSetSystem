@extends('base')

@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('thesaurus.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Thesauru</h1>
                <form class="uk-form" enctype="multipart/form-data">
                    <select>
                        <option>文字を入力して登録する</option>
                        <option>CSVファイルを入力して登録する</option>
                    </select>

                    <div class="uk-form-row">
                        <div class="tm-upload-button">
                            <i class="fa fa-plus"></i>
                            ファイルを選択
                            <input name='thesaurus' id="file-upload" type="file">
                        </div>
                    </div>
                    <button class="uk-button">更新</button>
                </form>
            </div>
        </div>
    </div>
@endsection