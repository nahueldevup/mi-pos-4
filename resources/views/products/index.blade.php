@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div classclass="container">
        {{--
          Simplemente llamamos al componente Livewire.
          Él se encargará de todo lo demás.
        --}}
        @livewire('show-products')
    </div>
@endsection
