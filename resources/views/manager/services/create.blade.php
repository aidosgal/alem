@extends('layouts.manager')

@section('title', 'Создать услугу')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('manager.services.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            ← Назад к услугам
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Создать новую услугу</h1>
        <p class="text-gray-600 mt-1">Заполните детали для создания услуги</p>
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

    <form method="POST" action="{{ route('manager.services.store') }}" class="bg-white rounded-2xl border border-gray-200 p-8">
        @csrf

        <div class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-900 mb-2">Название услуги *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('title') border-red-500 @enderror" 
                    placeholder="Например: Консультация по трудоустройству"
                    value="{{ old('title') }}"
                    required
                >
                @error('title')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Описание услуги</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="6"
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors resize-y focus:border-[#319885] @error('description') border-red-500 @enderror" 
                    placeholder="Опишите детали услуги, что входит, какие результаты..."
                >{{ old('description') }}</textarea>
                @error('description')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-900 mb-2">Цена (₸)</label>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]" 
                    placeholder="50000"
                    value="{{ old('price') }}"
                    min="0"
                    step="1"
                >
                <span class="block mt-1.5 text-xs text-gray-500">Оставьте пустым, если цена договорная</span>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Длительность выполнения</h3>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-900 mb-2">Точная длительность (дней)</label>
                        <input 
                            type="number" 
                            id="duration_days" 
                            name="duration_days" 
                            class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]" 
                            placeholder="7"
                            value="{{ old('duration_days') }}"
                            min="0"
                        >
                        <span class="block mt-1.5 text-xs text-gray-500">Или укажите диапазон ниже</span>
                    </div>

                    <div>
                        <label for="duration_min_days" class="block text-sm font-medium text-gray-900 mb-2">Минимум дней</label>
                        <input 
                            type="number" 
                            id="duration_min_days" 
                            name="duration_min_days" 
                            class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]" 
                            placeholder="5"
                            value="{{ old('duration_min_days') }}"
                            min="0"
                        >
                    </div>

                    <div>
                        <label for="duration_max_days" class="block text-sm font-medium text-gray-900 mb-2">Максимум дней</label>
                        <input 
                            type="number" 
                            id="duration_max_days" 
                            name="duration_max_days" 
                            class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]" 
                            placeholder="14"
                            value="{{ old('duration_max_days') }}"
                            min="0"
                        >
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 text-base font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961] hover:-translate-y-0.5">
                    Создать услугу
                </button>
                <a href="{{ route('manager.services.index') }}" class="px-6 py-3 text-base font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg transition-colors hover:bg-gray-50">
                    Отмена
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
