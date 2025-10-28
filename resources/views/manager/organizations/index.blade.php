@extends('layouts.manager')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Мои организации</h1>
        <div class="flex gap-3">
            <a href="{{ route('manager.organizations.join') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Присоединиться к организации
            </a>
            <a href="{{ route('manager.organizations.create') }}" 
               class="px-4 py-2 bg-[#EFFE6D] text-gray-900 font-medium rounded-lg hover:bg-[#EFFE6D]/90 transition-colors">
                + Создать организацию
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Current Organization -->
    @if($currentOrganization)
        <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium mb-1">✓ Текущая организация</p>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $currentOrganization->name }}</h2>
                    @if($currentOrganization->description)
                        <p class="text-gray-700 mb-3">{{ $currentOrganization->description }}</p>
                    @endif
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        @if($currentOrganization->address)
                            <span>📍 {{ $currentOrganization->address }}</span>
                        @endif
                        @if($currentOrganization->phone)
                            <span>📞 {{ $currentOrganization->phone }}</span>
                        @endif
                    </div>
                </div>
                <div class="px-3 py-1 bg-blue-600 text-white text-sm rounded-full">
                    Активна
                </div>
            </div>
        </div>
    @endif

    <!-- Owned Organizations -->
    @if($ownedOrganizations->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Мои организации (владелец)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($ownedOrganizations as $org)
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow {{ $currentOrganization && $currentOrganization->id === $org->id ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $org->name }}</h3>
                                <span class="inline-block px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                                    Владелец
                                </span>
                            </div>
                        </div>
                        
                        @if($org->description)
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($org->description, 100) }}</p>
                        @endif

                        <div class="text-sm text-gray-500 mb-4">
                            <p>Код для присоединения:</p>
                            <code class="block mt-1 px-3 py-2 bg-gray-100 rounded text-xs font-mono">{{ $org->id }}</code>
                        </div>

                        @if(!$currentOrganization || $currentOrganization->id !== $org->id)
                            <form action="{{ route('manager.organizations.switch', $org->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Переключиться
                                </button>
                            </form>
                        @else
                            <div class="text-center py-2 text-blue-600 font-medium">
                                ✓ Активна
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Member Organizations -->
    @if($memberOrganizations->count() > 0)
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Организации (участник)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($memberOrganizations as $org)
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow {{ $currentOrganization && $currentOrganization->id === $org->id ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $org->name }}</h3>
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                    Участник
                                </span>
                            </div>
                        </div>
                        
                        @if($org->description)
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($org->description, 100) }}</p>
                        @endif

                        @if(!$currentOrganization || $currentOrganization->id !== $org->id)
                            <form action="{{ route('manager.organizations.switch', $org->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Переключиться
                                </button>
                            </form>
                        @else
                            <div class="text-center py-2 text-blue-600 font-medium">
                                ✓ Активна
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($ownedOrganizations->count() === 0 && $memberOrganizations->count() === 0)
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🏢</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">У вас пока нет организаций</h3>
            <p class="text-gray-600 mb-6">Создайте новую организацию или присоединитесь к существующей</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('manager.organizations.create') }}" 
                   class="px-6 py-3 bg-[#EFFE6D] text-gray-900 font-medium rounded-lg hover:bg-[#EFFE6D]/90 transition-colors">
                    Создать организацию
                </a>
                <a href="{{ route('manager.organizations.join') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Присоединиться
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
