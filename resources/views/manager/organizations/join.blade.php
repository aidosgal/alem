@extends('layouts.manager')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Присоединиться к организации</h1>
        <p class="text-gray-600">Введите код организации для присоединения</p>
    </div>

    <!-- Error Messages -->
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('manager.organizations.join') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Код организации *
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code') }}"
                           required
                           placeholder="Введите код организации"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-mono">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        Код организации можно получить у владельца или администратора организации
                    </p>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('manager.organizations.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Отмена
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-[#319885] text-white font-medium rounded-lg hover:bg-[#319885]/90 transition-colors">
                    Присоединиться
                </button>
            </div>
        </form>
    </div>

    <!-- How It Works -->
    <div class="mt-6 space-y-4">
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-semibold text-blue-900 mb-2">📋 Как это работает?</h3>
            <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800">
                <li>Получите код организации у её владельца или администратора</li>
                <li>Введите код в форму выше</li>
                <li>После присоединения вы получите доступ к вакансиям, услугам и заказам организации</li>
            </ol>
        </div>

        <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <h3 class="font-semibold text-amber-900 mb-2">⚠️ Важно знать</h3>
            <ul class="list-disc list-inside space-y-1 text-sm text-amber-800">
                <li>Вы можете быть участником нескольких организаций одновременно</li>
                <li>Переключение между организациями доступно в разделе "Организации"</li>
                <li>Владелец организации может управлять участниками</li>
            </ul>
        </div>
    </div>
</div>
@endsection
