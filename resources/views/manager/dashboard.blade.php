@extends('layouts.app')

@section('title', 'Панель управления')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-5 py-5">
            <div class="flex items-center justify-between">
                <div class="text-2xl font-bold">Alem</div>
                
                <div class="flex items-center gap-4">
                    <div class="px-4 py-2 text-sm font-medium bg-gray-50 border-2 border-gray-200 rounded-lg">
                        🏢 {{ $currentOrganization->name ?? 'No Organization' }}
                    </div>

                    <div class="flex items-center gap-3 px-4 py-2 text-sm bg-gray-50 rounded-lg">
                        <span>{{ $manager->full_name ?? $manager->user->email }}</span>
                        <form action="{{ route('manager.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2 py-1 text-gray-600 hover:text-gray-900">Выйти</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-5 py-10">
        @if(session('success'))
            <div class="px-4 py-3 mb-6 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-10 mb-8 bg-white rounded-2xl">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">С возвращением, {{ $manager->first_name ?? 'Менеджер' }}! 👋</h1>
            <p class="text-gray-600">Управляйте вашими вакансиями и связывайтесь с талантливыми работниками СНГ, ищущими возможности в Европе.</p>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div class="p-6 bg-white rounded-xl">
                <div class="text-sm text-gray-600 mb-2">Активные вакансии</div>
                <div class="text-4xl font-bold text-gray-900">0</div>
            </div>
            <div class="p-6 bg-white rounded-xl">
                <div class="text-sm text-gray-600 mb-2">Всего кандидатов</div>
                <div class="text-4xl font-bold text-gray-900">0</div>
            </div>
            <div class="p-6 bg-white rounded-xl">
                <div class="text-sm text-gray-600 mb-2">Организации</div>
                <div class="text-4xl font-bold text-gray-900">{{ $organizations->count() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
