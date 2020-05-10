@extends('layout')

@section('content')
    <form method="POST" action="{{ route('money.update') }}">
        {{ csrf_field() }}
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
                            <td>
                                <input class="form-control" name="money[{{ $player->id }}]" value="{{ old('money')[$player->id] ?? $currentMoney->moneyPlayer($player)->money }}">
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        @error('money')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <button class="btn btn-primary">更新</button>
        <a class="btn btn-secondary" href="{{ route('home') }}">戻る</a>
    </form>
@endsection
