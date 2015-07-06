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

<a href="/review/add">新しいレビュー対象を追加する</a>
<table class="table table-bordered">
    <tr>
        <th>No.</th>
        <th>商品名</th>
        <th>レビュー数</th>
        <th>商品コード</th>
        <th>作成日時</th>
        <th>更新日時</th>
        <th>操作</th>
    </tr>
    @foreach($items as $item)
    <tr>
        <td>{{ $item->no }}</td>
        <td>{{ $item->name }}</td>
        <td>{{ $item->count }}</td>
        <td>{{ $item->item_code }}</td>
        <td>{{ $item->created }}</td>
        <td>{{ $item->updated }}</td>
        <td>
            <a href="/review/show/{{ $item->id }}"  class="btn btn-primary btn-sm">レビュー一覧</a>
            <a href="#update-review-modal" data-toggle="modal" data-id="{{ $item->id }}" class="update-review btn btn-success btn-sm">更新</a>
            <a href="#delete-review-modal" data-toggle="modal" data-id="{{ $item->id }}" class="modal-delete btn btn-danger btn-sm">削除</a>
        </td>
    </tr>
    @endforeach
</table>
@stop
@include('modal.update_review_modal')
@include('modal.delete_review_modal')
