@extends('layout.base')

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>基本語</th>
            <th>類義語</th>
            <th>分類</th>
            <th>作成日時</th>
            <th>更新日時</th>
        </tr>
        @foreach ($thesauruses as $thesaurus)
        <tr>
            <td>{{ $thesaurus->text }}</td>
            <td>{{ $thesaurus->synonym }}</td>
            <td>{{ $thesaurus->rayer }}</td>
            <td>{{ $thesaurus->created }}</td>
            <td>{{ $thesaurus->updated_at }}</td>
        </tr>
        @endforeach
    </table>
@stop