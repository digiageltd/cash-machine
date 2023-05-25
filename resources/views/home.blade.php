@extends('layout')

@section('content')
    <div class="p-10 text-center">
        <a href="{{ route('transaction.type', ['type'=>'cash']) }}"
           class="block border border-gray-500 rounded-md uppercase p-4 mb-2 text-white transition ease-in-out delay-150 bg-blue-500 hover:-translate-y-1 hover:scale-110 hover:bg-indigo-500 duration-300">Cash
            Transaction</a>
        <a href="{{ route('transaction.type', ['type'=>'credit_card']) }}"
           class="block border border-gray-500 rounded-md uppercase p-4 mb-2 text-white transition ease-in-out delay-150 bg-blue-500 hover:-translate-y-1 hover:scale-110 hover:bg-indigo-500 duration-300">Credit
            Card Transaction</a>
        <a href="{{ route('transaction.type', ['type'=>'bank_transfer']) }}" class="block border border-gray-500 rounded-md uppercase p-4 mb-2 text-white transition ease-in-out delay-150 bg-blue-500 hover:-translate-y-1 hover:scale-110 hover:bg-indigo-500 duration-300">Bank Transaction</a>
    </div>
@endsection
