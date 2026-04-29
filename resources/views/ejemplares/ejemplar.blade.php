@extends('layouts.app')

@section('titulo', 'Ejemplares')

@section('content')

    <div class="buscador">
        <div class="buscador__div">
            <form action="{{ route('ejemplar.buscar') }}" method="get">
                @csrf
                <button class="buscador__icono"><i class="fa fa-search"></i></button>
                <input type="search" class="buscador__input" name="ejemplar" placeholder="{{ __('Find a book...') }}" />
            </form>
        </div>
    </div>

    @if (session('error'))
        <div class="mensaje__error mensaje__error--center">
            <strong>{{ session('error') }}</strong>
        </div>
    @endif

    <div class="filtro">

        <p class="filtro__p">{{ $numero }} {{ trans_choice('messages.ejemplar', $numero) }}</p>

        <div id="mostrar-ordenar" class="ordenar">
            <p class="ordenar__p">{{ __('Sort by...') }}</p>
            <ul id="opciones-ordenar" class="ordenar__ul">
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 1) }}" class="ordenar__a">{{ __('Name') }} [a-z]</a>
                </li>
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 2) }}" class="ordenar__a">{{ __('Name') }}
                        [z-a]</a>
                </li>
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 3) }}" class="ordenar__a">{{ __('Older') }}</a></li>
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 4) }}" class="ordenar__a">{{ __('Newer') }}</a></li>
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 5) }}" class="ordenar__a">{{ __('Best Rated') }}</a>
                </li>
            </ul>
        </div>

    </div>

    @isset($resultado)
        <h2 class="ejemplar__resultado">{{ __('Result') }}: {{ $resultado }}</h2>
    @endisset

    <div class="ejemplares">
        @foreach ($ejemplares as $item)
            <div class="libro">
                <div class="libro__card">
                    <img src="{{ asset('book/' . $item->image_book) }}" alt="portada" class="libro__img">
                    <div class="libro__meta">
                        <h3 class="libro__titulo">{{ $item->nomEjemplar ?? __('Untitled') }}</h3>
                        @if(method_exists($item,'autor') && $item->codAutor)
                            @foreach($item->autor() as $a)
                                <p class="libro__autor">{{ $a->nomAutor }} {{ $a->ape1Autor }}</p>
                            @endforeach
                        @endif
                        <p class="libro__precio">{{ $item->precio }}€</p>
                        <a href="{{ route('ejemplar.ejemplar', $item) }}" class="libro__a">{{ __('View details') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    {{ $ejemplares->links() }}

@endsection
