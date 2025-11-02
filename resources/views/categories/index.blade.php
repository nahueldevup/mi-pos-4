@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
    <div class="container">
        {{-- Aquí "enchufas" tu componente Livewire --}}
        @livewire('show-categories')
    </div>
@endsection
