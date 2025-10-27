@extends('layouts.app')

@section('title', 'Создать организацию')

@section('content')
<div class="min-h-screen px-5 py-16 bg-gray-50">
    <div class="max-w-3xl p-12 mx-auto bg-white rounded-2xl">
        <div class="mb-10">
            <a href="{{ route('manager.organization.select') }}" class="inline-flex items-center mb-6 text-sm text-gray-600 hover:text-gray-900">
                ← Назад к выбору
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Создать организацию</h1>
            <p class="text-gray-600">Заполните детали для создания вашей организации</p>
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

        <form method="POST" action="{{ route('manager.organization.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Название организации *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('name') border-red-500 @enderror" 
                    placeholder="ТОО Компания"
                    value="{{ old('name') }}"
                    required
                >
                @error('name')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Описание</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors resize-y focus:border-[#319885] @error('description') border-red-500 @enderror" 
                    placeholder="Краткое описание вашей организации..."
                >{{ old('description') }}</textarea>
                @error('description')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email организации *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('email') border-red-500 @enderror" 
                    placeholder="contact@organization.com"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-900 mb-2">Телефон организации *</label>
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
                <label for="registration_document" class="block text-sm font-medium text-gray-900 mb-2">Документ регистрации</label>
                <label class="flex items-center justify-center w-full px-4 py-12 text-center text-gray-600 transition-colors bg-gray-50 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-[#319885] hover:bg-white">
                    <input 
                        type="file" 
                        id="registration_document" 
                        name="registration_document" 
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="hidden"
                        onchange="updateFileName(this, 'reg-file-name')"
                    >
                    <span id="reg-file-name">📄 Нажмите для загрузки документа регистрации</span>
                </label>
                <span class="block mt-1.5 text-xs text-gray-500">PDF, JPG, JPEG или PNG. Максимум 5МБ.</span>
                @error('registration_document')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="authority_document" class="block text-sm font-medium text-gray-900 mb-2">Документ полномочий</label>
                <label class="flex items-center justify-center w-full px-4 py-12 text-center text-gray-600 transition-colors bg-gray-50 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-[#319885] hover:bg-white">
                    <input 
                        type="file" 
                        id="authority_document" 
                        name="authority_document" 
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="hidden"
                        onchange="updateFileName(this, 'auth-file-name')"
                    >
                    <span id="auth-file-name">📄 Нажмите для загрузки документа полномочий</span>
                </label>
                <span class="block mt-1.5 text-xs text-gray-500">PDF, JPG, JPEG или PNG. Максимум 5МБ.</span>
                @error('authority_document')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full px-6 py-3.5 text-base font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961] hover:-translate-y-0.5 active:translate-y-0">
                Создать организацию
            </button>
        </form>
    </div>
</div>

<script>
    function updateFileName(input, spanId) {
        const span = document.getElementById(spanId);
        if (input.files.length > 0) {
            span.textContent = '✓ ' + input.files[0].name;
        }
    }
</script>
@endsection
