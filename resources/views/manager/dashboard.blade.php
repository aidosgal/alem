@extends('layouts.manager')

@section('title', 'Панель управления')

@section('content')
<div class="p-10 mb-8 bg-white rounded-2xl">
    <h1 class="text-4xl font-bold text-gray-900 mb-2">С возвращением, {{ $manager->first_name ?? 'Менеджер' }}! 👋</h1>
    <p class="text-gray-600">Управляйте вашими вакансиями и связывайтесь с талантливыми работниками СНГ, ищущими возможности в Европе.</p>
</div>

<div class="grid grid-cols-3 gap-5">
    <div class="p-6 bg-white rounded-xl border border-gray-200">
        <div class="text-sm text-gray-600 mb-2">Активные вакансии</div>
        <div class="text-4xl font-bold text-gray-900">0</div>
    </div>
    <div class="p-6 bg-white rounded-xl border border-gray-200">
        <div class="text-sm text-gray-600 mb-2">Всего кандидатов</div>
        <div class="text-4xl font-bold text-gray-900">0</div>
    </div>
    <div class="p-6 bg-white rounded-xl border border-gray-200">
        <div class="text-sm text-gray-600 mb-2">Организации</div>
        <div class="text-4xl font-bold text-gray-900">{{ $organizations->count() }}</div>
    </div>
</div>
@endsection
