@extends('layout')

@section('content')
    <h1>履歴</h1>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>終了日時</th>
                    @foreach($players as $player)
                        <th>{{ $player->name }}</th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if($currentMoneyGames->isNotEmpty())
                    @foreach($currentMoneyGames as $game)
                        <tr>
                            <td>{{ $game->finished_at }}</td>
                            @foreach($players as $player)
                                <td>{{ $game->calculatePlayerMoney($player) }}</td>
                            @endforeach
                            <td>
                                <a class="btn btn-primary btn-sm" href="{{ route('game.show', [$game]) }}">詳細</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection
