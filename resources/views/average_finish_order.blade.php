@extends('layout')

@section('content')
    <hr>
    <h1>平均着順</h1>
    <h2>三麻</h2>
    <table class="table">
        <thead>
            <tr>
                <th>名前</th>
                <th>平均着順</th>
            </tr>
        </thead>
        <tbody>
            @foreach($players as $player)
                <tr>
                    <td>{{ $player->name }}</td>
                    <td>{{ $averageFinishOrders3People->get($player->id) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
