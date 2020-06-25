@extends('layout')

@section('content')
    <div class="card">
        <div class="card-header">未精算</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>名前</th>
                            <th>金額</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentMoney->moneyPlayers as $moneyPlayer)
                            <tr>
                                <td>{{ $moneyPlayer->player->name }}</td>
                                <td>{{ $moneyPlayer->money }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a class="btn btn-warning" href="{{ route('money.edit') }}">金額修正</a>
            <a class="btn btn-secondary" href="{{ route('history') }}">履歴表示</a>
            <form method="POST" action="{{ route('money.reset') }}" onsubmit="return confirm('本当に精算しますか？');" class="d-inline">
                {{ csrf_field() }}
                <button class="btn btn-primary">精算</button>
            </form>
        </div>
    </div>
    @if($pastMoneys->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header">精算済</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                @foreach($players as $player)
                                    <th>{{ $player->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pastMoneys as $pastMoney)
                                <tr>
                                    @foreach($players as $player)
                                        <td>{{ $pastMoney->moneyPlayer($player)->money }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection
