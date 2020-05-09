@extends('layout')

@section('content')
    <hr>
    <h1>履歴</h1>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>終了日時</th>
                    @foreach($players as $player)
                        <th>{{ $player->name }}</th>
                    @endforeach
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
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <a class="btn btn-secondary" href="{{ route('home') }}">戻る</a>
@endsection
