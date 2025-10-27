@extends('layouts.manager')

@section('title', 'Создать статус')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('manager.order-statuses.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Назад к списку
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Создать статус</h1>
        <p class="mt-2 text-sm text-gray-600">Добавьте новый статус для управления заказами</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <form method="POST" action="{{ route('manager.order-statuses.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Название статуса <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent transition-colors @error('name') border-red-500 @enderror"
                       placeholder="Например: В работе"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug (оставьте пустым для автогенерации)
                </label>
                <input type="text" 
                       name="slug" 
                       id="slug" 
                       value="{{ old('slug') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent transition-colors font-mono text-sm @error('slug') border-red-500 @enderror"
                       placeholder="например: in_progress">
                <p class="mt-1 text-xs text-gray-500">Используется для идентификации статуса в системе</p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Color -->
            <div class="mb-6">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                    Цвет <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-3">
                    <input type="color" 
                           name="color" 
                           id="color" 
                           value="{{ old('color', '#3B82F6') }}"
                           class="w-20 h-11 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" 
                           name="color_text" 
                           id="color_text" 
                           value="{{ old('color', '#3B82F6') }}"
                           class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent transition-colors font-mono text-sm @error('color') border-red-500 @enderror"
                           placeholder="#3B82F6"
                           pattern="^#[0-9A-Fa-f]{6}$"
                           required>
                </div>
                <p class="mt-1 text-xs text-gray-500">Цвет отображается на канбан-доске</p>
                @error('color')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Order -->
            <div class="mb-6">
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                    Порядок сортировки
                </label>
                <input type="number" 
                       name="order" 
                       id="order" 
                       value="{{ old('order', 0) }}"
                       min="0"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent transition-colors @error('order') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Определяет порядок колонок на канбан-доске</p>
                @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200">
                <a href="{{ route('manager.order-statuses.index') }}" 
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Отмена
                </a>
                <button type="submit" 
                        class="px-4 py-2.5 text-sm font-medium text-gray-900 bg-[#EFFE6D] rounded-lg hover:bg-[#EFFE6D]/90 transition-colors">
                    Создать статус
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Sync color picker with text input
    const colorPicker = document.getElementById('color');
    const colorText = document.getElementById('color_text');
    
    colorPicker.addEventListener('input', (e) => {
        colorText.value = e.target.value.toUpperCase();
    });
    
    colorText.addEventListener('input', (e) => {
        const value = e.target.value;
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            colorPicker.value = value;
        }
    });
</script>
@endpush
@endsection
