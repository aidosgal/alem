@extends('layouts.manager')

@section('title', 'Услуги')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Услуги</h1>
        <p class="text-gray-600 mt-1">Управляйте услугами вашей организации</p>
    </div>
    <a href="{{ route('manager.services.create') }}" 
       class="px-6 py-3 text-sm font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961] hover:-translate-y-0.5">
        + Создать услугу
    </a>
</div>

@if($services->isEmpty())
    <div class="p-12 text-center bg-white rounded-2xl border border-gray-200">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Пока нет услуг</h3>
        <p class="text-gray-600 mb-6">Создайте вашу первую услугу для начала работы</p>
        <a href="{{ route('manager.services.create') }}" 
           class="inline-block px-6 py-3 text-sm font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961]">
            Создать первую услугу
        </a>
    </div>
@else
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Название
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Цена
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Длительность
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Создана
                    </th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Действия
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($services as $service)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $service->title }}</div>
                            @if($service->description)
                                <div class="text-sm text-gray-500 mt-1 line-clamp-1">{{ Str::limit($service->description, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($service->price)
                                {{ number_format($service->price, 2) }} ₸
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($service->duration_days)
                                {{ $service->duration_days }} {{ $service->duration_days == 1 ? 'день' : 'дней' }}
                            @elseif($service->duration_min_days && $service->duration_max_days)
                                {{ $service->duration_min_days }}–{{ $service->duration_max_days }} дней
                            @elseif($service->duration_min_days)
                                от {{ $service->duration_min_days }} дней
                            @elseif($service->duration_max_days)
                                до {{ $service->duration_max_days }} дней
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $service->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('manager.services.edit', $service->id) }}" 
                                   class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
                                    Редактировать
                                </a>
                                <form action="{{ route('manager.services.destroy', $service->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Вы уверены, что хотите удалить эту услугу?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded hover:bg-red-100 transition-colors">
                                        Удалить
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
