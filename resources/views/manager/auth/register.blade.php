@extends('layouts.auth')

@section('title', 'Регистрация')

@section('form')
<div class="mb-10">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Создать аккаунт</h2>
    <p class="text-gray-600">Начните управлять вакансиями для европейских возможностей</p>
</div>

@if($errors->any())
    <div class="px-4 py-3 mb-6 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">
        <ul class="pl-5 list-disc">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('manager.register') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-900 mb-2">Имя</label>
            <input 
                type="text" 
                id="first_name" 
                name="first_name" 
                class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('first_name') border-red-500 @enderror" 
                placeholder="Иван"
                value="{{ old('first_name') }}"
            >
            @error('first_name')
                <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-900 mb-2">Фамилия</label>
            <input 
                type="text" 
                id="last_name" 
                name="last_name" 
                class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('last_name') border-red-500 @enderror" 
                placeholder="Иванов"
                value="{{ old('last_name') }}"
            >
            @error('last_name')
                <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>
    </div>

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
        <label for="phone" class="block text-sm font-medium text-gray-900 mb-2">Номер телефона</label>
        <input 
            type="tel" 
            id="phone" 
            name="phone" 
            class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('phone') border-red-500 @enderror" 
            placeholder="+7 (777) 123-45-67"
            value="{{ old('phone') }}"
            required
        >
        @error('phone')
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
            placeholder="Минимум 8 символов"
            required
        >
        @error('password')
            <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">Подтвердите пароль</label>
        <input 
            type="password" 
            id="password_confirmation" 
            name="password_confirmation" 
            class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]" 
            placeholder="Повторите ваш пароль"
            required
        >
    </div>

    <button type="submit" class="w-full px-6 py-3.5 text-base font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961] hover:-translate-y-0.5 active:translate-y-0">
        Создать аккаунт
    </button>

    <div class="text-center text-sm text-gray-600">
        Уже есть аккаунт? <a href="{{ route('manager.login') }}" class="font-semibold text-[#319885] hover:underline">Войти</a>
    </div>
</form>
@endsection
