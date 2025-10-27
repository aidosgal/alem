@extends('layouts.manager')

@section('title', 'Редактировать вакансию')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('manager.vacancies.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            ← Назад к вакансиям
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Редактировать вакансию</h1>
        <p class="text-gray-600 mt-1">Обновите детали вакансии</p>
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

    <form method="POST" action="{{ route('manager.vacancies.update', $vacancy->id) }}" class="bg-white rounded-2xl border border-gray-200 p-8">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-900 mb-2">Название вакансии *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('title') border-red-500 @enderror" 
                    placeholder="Например: Frontend разработчик"
                    value="{{ old('title', $vacancy->title) }}"
                    required
                >
                @error('title')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Описание вакансии</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="6"
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors resize-y focus:border-[#319885] @error('description') border-red-500 @enderror" 
                    placeholder="Опишите требования, обязанности и условия работы..."
                >{{ old('description', $vacancy->description) }}</textarea>
                @error('description')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-900 mb-2">Локация</label>
                    <input 
                        type="text" 
                        id="location" 
                        name="location" 
                        class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]" 
                        placeholder="Например: Берлин, Германия"
                        value="{{ old('location', $vacancy->details['location'] ?? '') }}"
                    >
                </div>

                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-900 mb-2">Тип занятости</label>
                    <select 
                        id="employment_type" 
                        name="employment_type" 
                        class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]"
                    >
                        <option value="">Выберите тип</option>
                        <option value="Полная занятость" {{ old('employment_type', $vacancy->details['employment_type'] ?? '') == 'Полная занятость' ? 'selected' : '' }}>Полная занятость</option>
                        <option value="Частичная занятость" {{ old('employment_type', $vacancy->details['employment_type'] ?? '') == 'Частичная занятость' ? 'selected' : '' }}>Частичная занятость</option>
                        <option value="Контракт" {{ old('employment_type', $vacancy->details['employment_type'] ?? '') == 'Контракт' ? 'selected' : '' }}>Контракт</option>
                        <option value="Стажировка" {{ old('employment_type', $vacancy->details['employment_type'] ?? '') == 'Стажировка' ? 'selected' : '' }}>Стажировка</option>
                        <option value="Удаленная работа" {{ old('employment_type', $vacancy->details['employment_type'] ?? '') == 'Удаленная работа' ? 'selected' : '' }}>Удаленная работа</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="salary_from" class="block text-sm font-medium text-gray-900 mb-2">Зарплата от</label>
                    <input 
                        type="number" 
                        id="salary_from" 
                        name="salary_from" 
                        class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]" 
                        placeholder="1000"
                        value="{{ old('salary_from', $vacancy->details['salary_from'] ?? '') }}"
                        min="0"
                        step="100"
                    >
                </div>

                <div>
                    <label for="salary_to" class="block text-sm font-medium text-gray-900 mb-2">Зарплата до</label>
                    <input 
                        type="number" 
                        id="salary_to" 
                        name="salary_to" 
                        class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]" 
                        placeholder="3000"
                        value="{{ old('salary_to', $vacancy->details['salary_to'] ?? '') }}"
                        min="0"
                        step="100"
                    >
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-900 mb-2">Валюта</label>
                    <select 
                        id="currency" 
                        name="currency" 
                        class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885]"
                    >
                        <option value="EUR" {{ old('currency', $vacancy->details['currency'] ?? 'EUR') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="USD" {{ old('currency', $vacancy->details['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="GBP" {{ old('currency', $vacancy->details['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP</option>
                        <option value="KZT" {{ old('currency', $vacancy->details['currency'] ?? '') == 'KZT' ? 'selected' : '' }}>KZT</option>
                        <option value="RUB" {{ old('currency', $vacancy->details['currency'] ?? '') == 'RUB' ? 'selected' : '' }}>RUB</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 text-base font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961] hover:-translate-y-0.5">
                    Сохранить изменения
                </button>
                <a href="{{ route('manager.vacancies.index') }}" class="px-6 py-3 text-base font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg transition-colors hover:bg-gray-50">
                    Отмена
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
