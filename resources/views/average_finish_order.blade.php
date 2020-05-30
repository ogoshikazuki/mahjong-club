@extends('layout')

@section('content')
    <h1>平均着順</h1>
    <h2>四麻</h2>
    <table class="table">
        <thead>
            <tr>
                <th>名前</th>
                <th>平均着順</th>
            </tr>
        </thead>
        <tbody>
            @foreach($averageFinishOrders4People as $playerId => $averageFinishOrder)
                <tr>
                    <td>{{ App\Player::findOrFail($playerId)->name }}</td>
                    <td>{{ $averageFinishOrder }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h2>三麻</h2>
    <table class="table">
        <thead>
            <tr>
                <th>名前</th>
                <th>平均着順</th>
            </tr>
        </thead>
        <tbody>
            @foreach($averageFinishOrders3People as $playerId => $averageFinishOrder)
                <tr>
                    <td>{{ App\Player::findOrFail($playerId)->name }}</td>
                    <td>{{ $averageFinishOrder }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
