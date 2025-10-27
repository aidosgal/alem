@extends('layouts.app')

@section('title', 'Присоединиться к организации')

@section('content')
<div class="min-h-screen px-5 py-16 bg-gray-50">
    <div class="max-w-2xl p-12 mx-auto bg-white rounded-2xl">
        <div class="mb-10">
            <a href="{{ route('manager.organization.select') }}" class="inline-flex items-center mb-6 text-sm text-gray-600 hover:text-gray-900">
                ← Назад к выбору
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Присоединиться к организации</h1>
            <p class="text-gray-600">Введите ID организации для присоединения</p>
        </div>

        <div class="px-4 py-3 mb-6 text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-lg">
            💡 Запросите у администратора организации ID для присоединения к команде.
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

        <form method="POST" action="{{ route('manager.organization.join.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="organization_id" class="block text-sm font-medium text-gray-900 mb-2">ID организации *</label>
                <input 
                    type="text" 
                    id="organization_id" 
                    name="organization_id" 
                    class="w-full px-4 py-3 text-base border-2 border-gray-200 rounded-lg outline-none transition-colors focus:border-[#319885] @error('organization_id') border-red-500 @enderror" 
                    placeholder="Введите ID организации"
                    value="{{ old('organization_id') }}"
                    required
                >
                @error('organization_id')
                    <span class="block mt-1.5 text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full px-6 py-3.5 text-base font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961] hover:-translate-y-0.5 active:translate-y-0">
                Присоединиться к организации
            </button>
        </form>
    </div>
</div>
@endsection
