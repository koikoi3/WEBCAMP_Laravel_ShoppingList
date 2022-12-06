@extends('admin.layout')

{{-- メインコンテンツ --}}
@section('contets')
        <menu label="リンク">
         <a href="./top">管理画面Top</a><br>
        <a href="./user/list">ユーザ一覧</a><br>
        <a href="./logout">ログアウト</a><br>
        </menu>
        
        <h1>管理画面</h1>
@endsection