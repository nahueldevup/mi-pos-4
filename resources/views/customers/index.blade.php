@extends('layouts.app')
@section('title', 'Clientes')

@section('content')
    <div class="container">
        {{-- Aquí "enchufas" tu componente Livewire --}}
        @livewire('show-customers')
    </div>
@endsection
