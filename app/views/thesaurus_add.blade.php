@extends('layout.base')

@section('content')
    <form method="post" action="/thesaurus/add" enctype="multipart/form-data">
        <input type="file" name="thesaurus_csv">
        <input type="submit" class="btn btn-default btn-sm">
    </form>
@stop