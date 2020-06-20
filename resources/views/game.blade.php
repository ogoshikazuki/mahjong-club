@extends('layout')

@section('content')
    <game></game>
    <form method="POST" action="{{ route('game.finish') }}" onsubmit="return confirm('本当にゲームを終了しますか？');" class="d-inline">
        {{ csrf_field() }}
        <button class="btn btn-primary">ゲーム終了</button>
    </form>
    <form method="POST" action="{{ route('game.cancel') }}" onsubmit="return confirm('本当にゲームをキャンセルしますか？');"  class="d-inline">
        {{ csrf_field() }}
        <button class="btn btn-secondary">キャンセル</button>
    </form>
@endsection
