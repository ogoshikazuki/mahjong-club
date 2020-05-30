@extends('layout')

@section('content')
    <h1>平均着順</h1>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#players-4">四麻</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#players-3">三麻</a>
        </li>
    </ul>
    <div class="tab-content mt-3">
        <div id="players-4" class="tab-pane active">
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
        </div>
        <div id="players-3" class="tab-pane">
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
        </div>
    </div>
@endsection
