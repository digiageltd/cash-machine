@extends('layout')

@section('content')
    <div class="mt-6 mb-6">
        <h2 class="underline text-center text-3xl">There was an error!</h2>

        <div class="p-10 text-center">
            @error('amount')
            <h3 class="font-bold underline text-red-600 text-3xl">{{ $message }}</h3>
            @enderror
        </div>
        <h2 class="text-center text-2xl font-bold underline">
            <a href="{{ route('home') }}"><- Back to Home</a>
        </h2>
    </div>

@endsection
