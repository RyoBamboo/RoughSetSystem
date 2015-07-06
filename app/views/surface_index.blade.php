@extends('layout.base')

@section('content')

    <!-- alert -->
    @if (Session::get('alert'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>
            {{{ Session::get('alert') }}}
        </div>
    @endif


    <a href="/surface/add">新しい感性ワードを追加する</a>
    <table class="table table-bordered">
        <tr>
            <th>No.</th>
            <th>内容</th>
            <th>作成日時</th>
            <th>更新日時</th>
            <th>操作</th>
        </tr>
        @foreach($surfaces as $surface)
            <tr>
                <td>{{ $surface->no }}</td>
                <td>{{ $surface->content }}</td>
                <td>{{ $surface->created_at }}</td>
                <td>{{ $surface->updated_at }}</td>
                <td>
                    <a href="#update-review-modal" data-toggle="modal" data-id="{{ $surface->id }}" class="update-review btn btn-success btn-sm">編集</a>
                    <a href="#delete-surface-modal" data-toggle="modal" data-id="{{ $surface->id }}" class="modal-delete btn btn-danger btn-sm">削除</a>
                </td>
            </tr>
        @endforeach
    </table>
@stop
@include ('modal.delete_surface_modal')
