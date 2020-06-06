@extends('layout')

@section('content')
    <h2>入力</h2>
    <game-result-input @store-game-result="reloadGameResultHistory"></game-result-input>
    <hr>
    <h2>履歴</h2>
    <game-result-history ref="gameResultHistory"></game-result-history>
    <form method="POST" action="{{ route('game.finish') }}" onsubmit="return confirm('本当にゲームを終了しますか？');" class="d-inline">
        {{ csrf_field() }}
        <button class="btn btn-primary">ゲーム終了</button>
    </form>
    <form method="POST" action="{{ route('game.cancel') }}" onsubmit="return confirm('本当にゲームをキャンセルしますか？');"  class="d-inline">
        {{ csrf_field() }}
        <button class="btn btn-secondary">キャンセル</button>
    </form>
@endsection
