@extends('layout.base')

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>No.</th>
            <th>内容</th>
            <th>作成日時</th>
            <th>更新日時</th>
            <th>操作</th>
        </tr>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->created }}</td>
                <td>{{ $item->updated }}</td>
                <td>
                    <a href="/graph/view/" class="btn btn-primary btn-sm">グラフを見る</a>
                    <a href="/graph/make/" class="btn btn-default btn-sm">グラフを作成する</a>
                </td>
            </tr>
        @endforeach
    </table>
@stop