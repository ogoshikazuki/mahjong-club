@extends('layout')

@section('content')
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>レート</th>
                    @foreach($players as $player)
                        <th>{{ $player->name }}</th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($game->gameResults as $gameResult)
                    <tr>
                        <td>{{ $gameResult->rate }}</td>
                        @foreach($players as $player)
                            <td>{{ $gameResult->gameResultPlayer($player)->point ?? '' }}</td>
                        @endforeach
                        <td></td>
                    </tr>
                @empty
                @endforelse
                <tr>
                    <form method="POST" action="{{ route('game.result.store') }}">
                        {{ csrf_field() }}
                        <td>
                            <select name="rate" class="form-control form-control-sm" style="width: auto;">
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </td>
                        @foreach($players as $player)
                            <td>
                                <input name="points[{{ $player->id }}]" type="number" value="{{ old('points')[$player->id] ?? '' }}" class="form-control form-control-sm point">
                            </td>
                        @endforeach
                        <td>
                            <button class="btn btn-primary btn-sm">登録</button>
                        </td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>
    @error('points')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
    <form method="POST" action="{{ route('game.finish') }}" onsubmit="return confirm('本当にゲームを終了しますか？');" class="d-inline">
        {{ csrf_field() }}
        <button class="btn btn-primary">ゲーム終了</button>
    </form>
    <form method="POST" action="{{ route('game.cancel') }}" onsubmit="return confirm('本当にゲームをキャンセルしますか？');"  class="d-inline">
        {{ csrf_field() }}
        <button class="btn btn-secondary">キャンセル</button>
    </form>
@endsection
