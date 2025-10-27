@extends('layouts.app')

@section('title', 'Выбор организации')

@section('content')
<div class="flex items-center justify-center min-h-screen px-5 py-10 bg-gray-50">
    <div class="w-full max-w-4xl">
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Выберите свой путь</h1>
            <p class="text-lg text-gray-600">Создайте новую организацию или присоединитесь к существующей для начала работы</p>
        </div>

        @if(session('success'))
            <div class="px-4 py-3 mb-6 text-sm text-center text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-2 gap-6 mb-8">
            <a href="{{ route('manager.organization.create') }}" class="flex flex-col items-center p-10 text-center transition-all bg-white border-2 border-gray-200 rounded-2xl hover:border-[#319885] hover:-translate-y-1">
                <div class="flex items-center justify-center w-20 h-20 mb-6 text-4xl bg-gradient-to-br from-[#EFFE6D] to-[#f5f961] rounded-2xl">
                    🏢
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Создать организацию</h3>
                <p class="text-gray-600 leading-relaxed">Начните с нуля, создав свою организацию и пригласив членов команды</p>
            </a>

            <a href="{{ route('manager.organization.join') }}" class="flex flex-col items-center p-10 text-center transition-all bg-white border-2 border-gray-200 rounded-2xl hover:border-[#319885] hover:-translate-y-1">
                <div class="flex items-center justify-center w-20 h-20 mb-6 text-4xl text-white bg-gradient-to-br from-[#319885] to-[#2a8070] rounded-2xl">
                    🤝
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Присоединиться к организации</h3>
                <p class="text-gray-600 leading-relaxed">Присоединитесь к существующей организации используя код приглашения</p>
            </a>
        </div>

        <div class="text-center">
            <a href="{{ route('manager.logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-sm text-gray-600 hover:text-gray-900 hover:underline">
                Выйти
            </a>
            <form id="logout-form" action="{{ route('manager.logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection
