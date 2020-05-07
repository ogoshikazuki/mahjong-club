@extends('layout')

@section('content')
    <table class="table">
        <thead>
            <tr>
                @foreach($players as $player)
                    <th>{{ $player->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($players as $player)
                    <td>{{ $currentPoint->pointPlayer($player)->point }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <a class="btn btn-warning" href="{{ route('money.edit') }}">金額修正</a>
    <hr>
    <form method="POST" action="{{ route('game.start') }}">
        {{ csrf_field() }}
        <button class="btn btn-primary">ゲームスタート</button>
    </form>
@endsection
