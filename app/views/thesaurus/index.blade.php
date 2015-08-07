@extends('base')

@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('thesaurus.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Thesauru</h1>
                <table class="uk-table">
                    <thead>
                        <tr>
                            <th>基本語</th>
                            <th>類義語</th>
                            <th>分類</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($thesauruses as $thesaurus)
                        <tr>
                            <td>{{ $thesaurus->text }}</td>
                            <td>{{ $thesaurus->synonym }}</td>
                            <td>{{ $thesaurus->rayer }}</td>
                            <td>
                                <button data-uk-modal="{target:'#my-id'}" data-id="{{ $thesaurus->id }}" class="uk-button uk-button-danger">削除</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('thesaurus.delete_modal')
@endsection