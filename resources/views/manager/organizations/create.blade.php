@extends('layouts.manager')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Создать организацию</h1>
        <p class="text-gray-600">Заполните информацию о вашей организации</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('manager.organizations.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Название организации *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           placeholder="ТОО Alem"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Описание
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              placeholder="Краткое описание вашей организации..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Адрес
                    </label>
                    <input type="text" 
                           id="address" 
                           name="address" 
                           value="{{ old('address') }}"
                           placeholder="г. Алматы, ул. Абая 150"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Телефон
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           placeholder="+7 777 777 7777"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('manager.organizations.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Отмена
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-[#EFFE6D] text-gray-900 font-medium rounded-lg hover:bg-[#EFFE6D]/90 transition-colors">
                    Создать организацию
                </button>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-800">
            <strong>💡 Совет:</strong> После создания организации вы получите уникальный код, который можно использовать для приглашения других менеджеров.
        </p>
    </div>
</div>
@endsection
