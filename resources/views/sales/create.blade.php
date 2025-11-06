@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
    <div class="container">
        {{-- Aquí "enchufas" tu componente de Punto de Venta --}}
        @livewire('pos-component')
    </div>
@endsection
