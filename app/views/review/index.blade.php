@extends('base')

@section('content')
    <div class="tm-menubar uk-width-1-1">
        @include('review.menubar')
    </div>
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Review</h1><br>
                {{ $items->links(); }}
                <table class="uk-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>商品名</th>
                            <th>レビュー数</th>
                            <th>作成日時</th>
                            <th>更新日時</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->no }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->count }}</td>
                            <td>{{ $item->created }}</td>
                            <td>{{ $item->updated }}</td>
                            <td>
                                <a href="/review/show/{{ $item->id }}"  class="uk-button uk-button-small">レビュー一覧</a>
                                <a data-uk-modal="{target:'#my-id'}" data-id="{{ $item->id }}" class="modal-delete uk-button uk-button-small uk-button-danger">削除</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('review.delete_modal')
@endsection