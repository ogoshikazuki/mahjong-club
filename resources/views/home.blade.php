@extends('layout')

@section('content')
    <hr>
    <h2>未精算</h2>
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
                <tr>
                    @foreach($players as $player)
                        <td>{{ $currentMoney->moneyPlayer($player)->money }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <form method="POST" action="{{ route('money.reset') }}" onsubmit="return confirm('本当に精算しますか？');">
        {{ csrf_field() }}
        <a class="btn btn-warning" href="{{ route('money.edit') }}">金額修正</a>
        <button class="btn btn-primary">精算</button>
    </form>
    <hr>
    <form method="POST" action="{{ route('game.start') }}">
        {{ csrf_field() }}
        <button class="btn btn-primary">ゲームスタート</button>
    </form>
    @if($pastMoneys->isNotEmpty())
        <hr>
        <h2>精算済</h2>
        <table class="table">
            <thead>
                <tr>
                    @foreach($players as $player)
                        <th>{{ $player->name }}</th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($pastMoneys as $pastMoney)
                    <tr>
                        @foreach($players as $player)
                            <td>{{ $pastMoney->moneyPlayer($player)->money }}</td>
                        @endforeach
                        <td>
                            <form method="POST" action="{{ route('money.delete', [$pastMoney->id]) }}" onsubmit="return confirm('削除すると元に戻せません。よろしいですか？');">
                                {{ csrf_field() }}
                                <button class="btn btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
