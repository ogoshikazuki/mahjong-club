@extends('layout')

@section('content')
    <form method="POST" action="{{ route('money.update') }}">
        {{ csrf_field() }}
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
                            <input class="form-control" name="money[{{ $player->id }}]" value="{{ old('money')[$player->id] ?? $currentmoney->moneyPlayer($player)->point }}">
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        @error('money')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <button class="btn btn-primary">更新</button>
    </form>
@endsection
