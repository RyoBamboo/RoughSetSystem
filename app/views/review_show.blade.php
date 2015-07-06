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
<table class="table table-bordered">
    <tr>
        <th>No.</th>
        <th>内容</th>
        <th>作成日時</th>
        <th>更新日時</th>
    </tr>
    @foreach($reviews as $review)
    <tr>
        <td>{{ $review->no }}</td>
        <td>{{ $review->content }}</td>
        <td>{{ $review->created_at }}</td>
        <td>{{ $review->updated_at }}</td>
    </tr>
    @endforeach
</table>
@stop