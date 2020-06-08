@extends('layout')

@section('content')
    <hr>
    <h1>ゲーム詳細</h1>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>レート</th>
                    @foreach($players as $player)
                        <th>{{ $player->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($game->gameResults as $gameResult)
                    <tr>
                        <td>{{ $gameResult->rate }}</td>
                        @foreach($players as $player)
                            <td>
                                @php
                                    $gameResultPlayer = $gameResult->gameResultPlayer($player)
                                @endphp
                                @isset($gameResultPlayer)
                                    {{ $gameResultPlayer->point }}({{ $gameResultPlayer->tip }}枚)
                                @endisset
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <a class="btn btn-secondary" href="{{ route('history') }}">戻る</a>
@endsection
