@extends('layouts.manager')

@section('title', 'Статусы заказов')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Статусы заказов</h1>
            <p class="mt-2 text-sm text-gray-600">Управляйте статусами заказов вашей организации</p>
        </div>
        <a href="{{ route('manager.order-statuses.create') }}" 
           class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-900 bg-[#EFFE6D] rounded-lg hover:bg-[#EFFE6D]/90 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Добавить статус
        </a>
    </div>

    @if($statuses->isEmpty())
        <!-- Empty state -->
        <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Нет статусов</h3>
            <p class="text-gray-600 mb-6">Сначала создайте статусы для управления заказами</p>
            <div class="flex gap-3 justify-center">
                <form method="POST" action="{{ route('manager.order-statuses.initialize') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-[#319885] rounded-lg hover:bg-[#319885]/90 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Создать стандартные статусы
                    </button>
                </form>
                <a href="{{ route('manager.order-statuses.create') }}" 
                   class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-900 bg-[#EFFE6D] rounded-lg hover:bg-[#EFFE6D]/90 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Создать свой статус
                </a>
            </div>
        </div>
    @else
        <!-- Statuses list -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Статус
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Slug
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Цвет
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Порядок
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Заказов
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($statuses as $status)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                                              style="background-color: {{ $status->color }}20; color: {{ $status->color }}">
                                            {{ $status->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">
                                    {{ $status->slug }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded border border-gray-300" 
                                             style="background-color: {{ $status->color }}"></div>
                                        <span class="text-xs text-gray-600 font-mono">{{ $status->color }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $status->order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $status->orders->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('manager.order-statuses.edit', $status->id) }}" 
                                           class="text-[#319885] hover:text-[#319885]/80">
                                            Изменить
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('manager.order-statuses.destroy', $status->id) }}"
                                              onsubmit="return confirm('Вы уверены, что хотите удалить этот статус?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
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
        </div>

        <!-- Info -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">О статусах заказов</h4>
                    <p class="mt-1 text-sm text-blue-700">
                        Статусы позволяют отслеживать состояние заказов на канбан-доске. 
                        Вы можете создавать собственные статусы с уникальными названиями и цветами.
                        Порядок определяет последовательность колонок на доске.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
