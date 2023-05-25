@extends('layout')

@section('content')
    <div class="p-5 mt-6 mb-6">
        <h2 class="underline text-3xl text-center mb-4">{{ trans('cash_machine.successful.transaction') }}</h2>

        <div class="p-5 border rounded-md mb-4">
            <h3 class="font-bold underline text-2xl mb-2">{{ trans('cash_machine.transaction.details') }}:</h3>
            <ul>
                <li class="text-xl"><span
                        class="font-bold">{{ trans('cash_machine.transaction.id') }}:</span> {{ $transaction->id }}
                </li>
                <li class="text-xl"><span
                        class="font-bold">{{ trans('cash_machine.transaction.total.amount') }}:</span> {{ $transaction->amount }}
                </li>
            </ul>
        </div>

        <div class="p-5 border rounded-md bg-black text-white">
            <h3 class="font-bold underline">{{ trans('cash_machine.transaction.inputs') }}:</h3>
            <ul>
                @foreach($transaction->inputs as $key=>$value)
                    @if(is_array($value))
                        <li>
                            <span class="font-bold">{{ $key }}:</span>
                            <ul class="ml-6">
                                @foreach($value as $subKey => $subValue)
                                    <li><span class="font-bold">Banknote {{ $subKey }}:</span> {{ $subValue ?? '0' }} pcs</li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li>
                            <span class="font-bold">{{ $key }}:</span> {{ $value }}
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
    <h2 class="text-center text-2xl font-bold underline">
        <a href="{{ route('home') }}"><- {{ trans('cash_machine.button.back.home') }}</a>
    </h2>
@endsection
