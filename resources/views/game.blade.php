@extends('layout')

@section('content')
    @if(session()->has('temporaryGameErrorMessage'))
        <div class="alert alert-danger">{{ session('temporaryGameErrorMessage') }}</div>
    @endif
    <h2>入力</h2>
    @error('points')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
    <form method="POST" action="{{ route('game.result.store') }}">
        <table class="table">
            {{ csrf_field() }}
            <tr>
                <th>レート</th>
                <th>
                    <select name="rate" class="form-control form-control-sm" style="width: auto;">
                        @foreach(Constant\Rate::getConstants() as $rate)
                            <option value="{{ $rate }}">{{ $rate }}</option>
                        @endforeach
                    </select>
                </th>
            </tr>
            @foreach($players as $player)
                <tr>
                    <th>{{ $player->name }}</th>
                    <td>
                        <input name="points[{{ $player->id }}]" type="number" value="{{ old('points')[$player->id] ?? '' }}" class="form-control form-control-sm">
                    </td>
                </tr>
            @endforeach
        </table>
        <button class="btn btn-primary">登録</button>
    </form>
    <hr>
    <h2>履歴</h2>
    <game-result-history></game-result-history>
    <form method="POST" action="{{ route('game.finish') }}" onsubmit="return confirm('本当にゲームを終了しますか？');" class="d-inline">
        {{ csrf_field() }}
        <button class="btn btn-primary">ゲーム終了</button>
    </form>
    <form method="POST" action="{{ route('game.cancel') }}" onsubmit="return confirm('本当にゲームをキャンセルしますか？');"  class="d-inline">
        {{ csrf_field() }}
        <button class="btn btn-secondary">キャンセル</button>
    </form>
@endsection
