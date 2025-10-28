@extends('layouts.manager')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Профиль</h1>
        <a href="{{ route('manager.profile.edit') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Редактировать профиль
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Личная информация</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-600">Полное имя</label>
                        <p class="text-base font-medium text-gray-900">{{ $user->name }}</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Имя</label>
                        <p class="text-base font-medium text-gray-900">{{ $manager->first_name }}</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Фамилия</label>
                        <p class="text-base font-medium text-gray-900">{{ $manager->last_name }}</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Телефон</label>
                        <p class="text-base font-medium text-gray-900">{{ $manager->phone ?? 'Не указан' }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Информация об аккаунте</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-600">Email</label>
                        <p class="text-base font-medium text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Роль</label>
                        <p class="text-base font-medium text-gray-900">Менеджер</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Дата регистрации</label>
                        <p class="text-base font-medium text-gray-900">{{ $user->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Organizations Section -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Мои организации</h2>
            <a href="{{ route('manager.organizations.index') }}" 
               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Управление →
            </a>
        </div>

        @php
            $currentOrg = $manager->currentOrganization();
        @endphp

        @if($currentOrg)
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-600 mb-1">Текущая организация</p>
                <p class="font-medium text-gray-900">{{ $currentOrg->name }}</p>
            </div>
        @else
            <p class="text-gray-600">Вы не выбрали организацию</p>
        @endif
    </div>
</div>
@endsection
