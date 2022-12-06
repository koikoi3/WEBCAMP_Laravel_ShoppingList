@extends('layout')

{{-- タイトル --}}
@section('title')(詳細画面)@endsection

{{-- メインコンテンツ --}}
@section('contets')
        <h1>ユーザ登録</h1>
            @if ($errors->any())
                <div>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
                </div>
            @endif
        <form action="/user/register" method="post">
            @csrf
            名前:<input name="name" value="{{ old('name') }}"><br> 
            email:<input name="email" value="{{ old('email') }}"><br>
            パスワード:<input name="password" type="password"><br>
            パスワードをもう一度入力:<input name="password_confirmation" type="password" autocomplete="new-password"><br>
            <button>登録する</button><br>
        </form>
@endsection