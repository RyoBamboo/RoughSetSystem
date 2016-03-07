@extends('base')

@section('content')
    <div class="tm-menubar uk-width-1-1">
        @include('thesaurus.menubar')
    </div>
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Thesauru</h1>
                <table class="uk-table">
                    <thead>
                        <tr>
                            <th>基本語</th>
                            <th>類義語</th>
                            <th>分類</th>
                            <th>操作</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($thesauruses as $thesaurus)
                        <tr>
                            <td>{{ $thesaurus->text }}</td>
                            <td id="_{{ $thesaurus->id}}">{{ $thesaurus->synonym }}</td>
                            <td id="{{ $thesaurus->id}}">{{ $thesaurus->rayer }}</td>
                            <td>
                                <button data-uk-modal="{target:'#update'}" data-id="{{ $thesaurus->id }}" class="modal-update uk-button uk-button-primary">変更</button>
                            </td>
                            <td>
                                <button data-uk-modal="{target:'#delete'}" data-id="{{ $thesaurus->id }}" class="modal-delete uk-button uk-button-danger">削除</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('thesaurus.delete_modal')
    @include('thesaurus.update_modal')
@endsection