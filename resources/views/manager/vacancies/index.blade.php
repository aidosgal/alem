@extends('layouts.manager')

@section('title', 'Вакансии')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Вакансии</h1>
        <p class="text-gray-600 mt-1">Управляйте вашими открытыми позициями</p>
    </div>
    <a href="{{ route('manager.vacancies.create') }}" 
       class="px-6 py-3 text-sm font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961] hover:-translate-y-0.5">
        + Создать вакансию
    </a>
</div>

@if($vacancies->isEmpty())
    <div class="p-12 text-center bg-white rounded-2xl border border-gray-200">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Пока нет вакансий</h3>
        <p class="text-gray-600 mb-6">Создайте вашу первую вакансию, чтобы начать поиск талантов</p>
        <a href="{{ route('manager.vacancies.create') }}" 
           class="inline-block px-6 py-3 text-sm font-semibold text-gray-900 bg-[#EFFE6D] rounded-lg transition-all hover:bg-[#f5f961]">
            Создать первую вакансию
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
                        Локация
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Зарплата
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Тип занятости
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
                @foreach($vacancies as $vacancy)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $vacancy->title }}</div>
                            @if($vacancy->description)
                                <div class="text-sm text-gray-500 mt-1 line-clamp-1">{{ Str::limit($vacancy->description, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $vacancy->details['location'] ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if(isset($vacancy->details['salary_from']) || isset($vacancy->details['salary_to']))
                                @if(isset($vacancy->details['salary_from']) && isset($vacancy->details['salary_to']))
                                    {{ number_format($vacancy->details['salary_from']) }}–{{ number_format($vacancy->details['salary_to']) }} {{ $vacancy->details['currency'] ?? '₸' }}
                                @elseif(isset($vacancy->details['salary_from']))
                                    от {{ number_format($vacancy->details['salary_from']) }} {{ $vacancy->details['currency'] ?? '₸' }}
                                @else
                                    до {{ number_format($vacancy->details['salary_to']) }} {{ $vacancy->details['currency'] ?? '₸' }}
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $vacancy->details['employment_type'] ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $vacancy->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('manager.vacancies.edit', $vacancy->id) }}" 
                                   class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
                                    Редактировать
                                </a>
                                <form action="{{ route('manager.vacancies.destroy', $vacancy->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Вы уверены, что хотите удалить эту вакансию?')">
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
