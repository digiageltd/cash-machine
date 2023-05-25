@extends('layout')

@section('content')
    <div class="p-5 mb-6">
        <form method="POST" action="{{ route('transaction.store') }}">
            @csrf
            <input type="hidden" name="transaction_type" value="{!! $type !!}">
            <div class="mt-4">
                @if($type == 'cash')
                    @foreach ($quantities as $quantity)
                        <div>
                            <label for="quantity[{{ $quantity }}]">
                                {{ trans('cash_machine.form.quantity', ['quantity' => $quantity]) }}
                            </label>
                            <input type="number" name="quantity[{{ $quantity }}]"
                                   value="{{ old('quantity['.$quantity.']') ?? 0 }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">
                            @error('quantity.'.$quantity)
                            <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span>
                            </p>
                            @enderror
                        </div>
                    @endforeach
                @else
                    <div>
                        <label for="amount">
                            {{ trans('cash_machine.form.amount') }}
                        </label>
                        <input type="number" name="amount" placeholder="1000" value="{{ old('amount') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">
                        @error('amount')
                        <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span></p>
                        @enderror
                    </div>
                @endif
                @if($type == 'credit_card')
                    <div class="mt-2">
                        <label for="cardNumber">
                            {{ trans('cash_machine.form.card.number') }}
                        </label>
                        <input type="number" name="cardNumber" placeholder="**** **** **** ****"
                               value="{{ old('cardNumber') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">
                        @error('cardNumber')
                        <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span></p>
                        @enderror
                    </div>
                    <div class="mt-2">
                        <label for="cardHolder">
                            {{ trans('cash_machine.form.card.holder') }}
                        </label>
                        <input type="text" name="cardHolder" value="{{ old('cardHolder') }}"
                               placeholder="{{ trans('cash_machine.form.card.holder.placeholder') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">
                        @error('cardHolder')
                        <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span></p>
                        @enderror
                    </div>
                    <div class="mt-2">
                        <label for="cvv">
                            {{ trans('cash_machine.form.card.cvv') }}
                        </label>
                        <input type="number" name="cvv" min="0" oninput="this.value = this.value.slice(0, 3)"
                               value="{{ old('cvv') }}"
                               placeholder="***"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">
                        @error('cvv')
                        <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span></p>
                        @enderror
                    </div>
                    <div class="mt-2">
                        <label for="expirationDate">
                            {{ trans('cash_machine.form.card.expiration') }}
                        </label>
                        <input type="text" name="expirationDate" placeholder="2026-6 (YYYY-M)"
                               value="{{ old('expirationDate') }}"
                               oninput="formatExpiration(this)"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">

                        @error('expirationDate')
                        <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span></p>
                        @enderror
                    </div>
                @endif
                @if($type == 'bank_transfer')
                    <div class="mt-2">
                        <label for="customerName">
                            {{ trans('cash_machine.form.card.customer.name') }}
                        </label>
                        <input type="text" name="customerName" value="{{ old('customerName') }}"
                               placeholder="{{ trans('cash_machine.form.card.customer.name') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">
                        @error('customerName')
                        <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span></p>
                        @enderror
                    </div>
                    <div class="mt-2">
                        <label for="accountNumber">
                            {{ trans('cash_machine.form.card.account.number') }}
                        </label>
                        <input type="text" name="accountNumber" value="{{ old('accountNumber') }}"
                               placeholder="******" oninput="formatAccountNumber(this)"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">
                        @error('accountNumber')
                        <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span></p>
                        @enderror
                    </div>
                    <div class="mt-2">
                        <label for="transferDate">
                            {{ trans('cash_machine.form.card.transfer.date') }}
                        </label>
                        <input type="text" name="transferDate" value="{{ old('transferDate') }}"
                               placeholder="YYYY-MM-DD" oninput="formatDate(this)"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 block w-full p-2.5">
                        @error('transferDate')
                        <p class="mt-1 mb-2 text-sm text-red-600"><span class="font-medium">{{ $message }}</span></p>
                        @enderror
                    </div>
                @endif
                <div class="mt-4">
                    <button
                        class="w-full border border-gray-500 rounded-md uppercase px-6 py-2 mb-4 text-white transition ease-in-out delay-150 bg-blue-500 hover:-translate-y-1 hover:scale-110 hover:bg-indigo-500 duration-300">{{ trans('cash_machine.form.button.store') }}</button>
                </div>
            </div>
        </form>
    </div>
    <h2 class="text-center text-2xl font-bold underline">
        <a href="{{ route('home') }}"><- {{ trans('cash_machine.button.back.home') }}</a>
    </h2>
@endsection
