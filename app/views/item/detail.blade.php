@extends('base')

@section('css')
@endsection

@section('js')
    <script src="/assets/js/item.js"></script>
@endsection

@section('content')
<button id="startAnalysis" data-item-id='{{ $item_id }}'>分析開始</button>
@endsection