@extends('layouts.auth')

@section('title', 'Вход')

@section('form')
<div class="mb-10">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">С возвращением</h2>
    <p class="text-gray-600">Введите свои данные для входа в аккаунт</p>
</div>

@if(session('success'))
    <div class="px-4 py-3 mb-6 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="px-4 py-3 mb-6 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">
        <ul class="pl-5 list-disc">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('manager.login') }}" class="space-y-6">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email адрес</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('email') border-red-500 @enderror" 
            placeholder="you@example.com"
            value="{{ old('email') }}"
            required
            autofocus
        >
        @error('email')
            <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-900 mb-2">Пароль</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('password') border-red-500 @enderror" 
            placeholder="Введите ваш пароль"
            required
        >
        @error('password')
            <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex items-center">
        <input 
            type="checkbox" 
            id="remember" 
            name="remember" 
            class="w-4 h-4 accent-[#319885] cursor-pointer"
            {{ old('remember') ? 'checked' : '' }}
        >
        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">Запомнить меня</label>
    </div>

    <button type="submit" class="w-full px-6 py-3.5 text-base font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961] hover:-translate-y-0.5 active:translate-y-0">
        Войти
    </button>

    <div class="text-center text-sm text-gray-600">
        Нет аккаунта? <a href="{{ route('manager.register') }}" class="font-semibold text-[#319885] hover:underline">Создать</a>
    </div>
</form>
@endsection
