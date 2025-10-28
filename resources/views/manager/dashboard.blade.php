@extends('layouts.manager')

@section('title', 'Панель управления')

@section('content')
<!-- Welcome Section -->
<div class="p-6 mb-6 bg-white rounded-lg border border-gray-200">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">С возвращением, {{ $manager->first_name ?? 'Менеджер' }}</h1>
    <p class="text-sm text-gray-600">{{ $currentOrganization ? 'Организация: ' . $currentOrganization->name : 'Выберите организацию для начала работы' }}</p>
</div>

@if(!$currentOrganization)
    <div class="p-6 bg-white border border-gray-300 rounded-lg">
        <h2 class="text-lg font-bold text-gray-900 mb-2">Организация не выбрана</h2>
        <p class="text-gray-600 mb-4">Для просмотра аналитики выберите или создайте организацию</p>
        <a href="{{ route('manager.organizations.index') }}" class="inline-block px-5 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
            Перейти к организациям
        </a>
    </div>
@else

<!-- Main Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Vacancies Card -->
    <div class="bg-white rounded-lg border border-gray-200 p-5 hover:border-gray-300 transition-colors">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2 bg-gray-100 rounded">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">
                {{ $stats['vacancies']['active'] }} активных
            </span>
        </div>
        <h3 class="text-xs font-medium text-gray-600 mb-1 uppercase tracking-wide">Вакансии</h3>
        <div class="flex items-end justify-between">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['vacancies']['total'] }}</p>
            <a href="{{ route('manager.vacancies.index') }}" class="text-xs text-gray-700 hover:text-gray-900 font-medium">
                Открыть →
            </a>
        </div>
    </div>

    <!-- Services Card -->
    <div class="bg-white rounded-lg border border-gray-200 p-5 hover:border-gray-300 transition-colors">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2 bg-gray-100 rounded">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">
                {{ number_format($stats['services']['total_value'], 0, ',', ' ') }} ₸
            </span>
        </div>
        <h3 class="text-xs font-medium text-gray-600 mb-1 uppercase tracking-wide">Услуги</h3>
        <div class="flex items-end justify-between">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['services']['total'] }}</p>
            <a href="{{ route('manager.services.index') }}" class="text-xs text-gray-700 hover:text-gray-900 font-medium">
                Открыть →
            </a>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="bg-white rounded-lg border border-gray-200 p-5 hover:border-gray-300 transition-colors">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2 bg-gray-100 rounded">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">
                {{ $stats['orders']['in_progress'] }} в работе
            </span>
        </div>
        <h3 class="text-xs font-medium text-gray-600 mb-1 uppercase tracking-wide">Заказы</h3>
        <div class="flex items-end justify-between">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['orders']['total'] }}</p>
            <a href="{{ route('manager.orders.index') }}" class="text-xs text-gray-700 hover:text-gray-900 font-medium">
                Открыть →
            </a>
        </div>
    </div>

    <!-- Chats Card -->
    <div class="bg-white rounded-lg border border-gray-200 p-5 hover:border-gray-300 transition-colors">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2 bg-gray-100 rounded">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            @if($stats['chats']['unread'] > 0)
                <span class="text-xs font-medium text-gray-900 bg-gray-200 px-2 py-1 rounded">
                    {{ $stats['chats']['unread'] }} непрочитанных
                </span>
            @else
                <span class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded">
                    Все прочитаны
                </span>
            @endif
        </div>
        <h3 class="text-xs font-medium text-gray-600 mb-1 uppercase tracking-wide">Чаты</h3>
        <div class="flex items-end justify-between">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['chats']['total'] }}</p>
            <a href="{{ route('manager.chat.index') }}" class="text-xs text-gray-700 hover:text-gray-900 font-medium">
                Открыть →
            </a>
        </div>
    </div>
</div>

<!-- Revenue & Orders Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <h3 class="text-xs font-medium text-gray-600 mb-2 uppercase tracking-wide">Общая выручка</h3>
        <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['orders']['total_value'], 0, ',', ' ') }} ₸</p>
        <p class="text-xs text-gray-500">Из {{ $stats['orders']['total'] }} заказов</p>
    </div>
    
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <h3 class="text-xs font-medium text-gray-600 mb-2 uppercase tracking-wide">Средний чек</h3>
        <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['orders']['avg_value'], 0, ',', ' ') }} ₸</p>
        <p class="text-xs text-gray-500">На один заказ</p>
    </div>
    
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <h3 class="text-xs font-medium text-gray-600 mb-2 uppercase tracking-wide">Активность сегодня</h3>
        <p class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['chats']['active_today'] }}</p>
        <p class="text-xs text-gray-500">Активных чатов</p>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Orders Timeline Chart -->
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Заказы за неделю</h2>
        <div class="h-64">
            <canvas id="ordersChart"></canvas>
        </div>
    </div>

    <!-- Revenue Timeline Chart -->
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Выручка за неделю</h2>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Orders by Status -->
<div class="bg-white rounded-lg border border-gray-200 p-5 mb-6">
    <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Заказы по статусам</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($ordersByStatus as $item)
            <div class="p-4 rounded-lg border-2" style="border-color: {{ $item['color'] }}">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $item['color'] }}"></div>
                    <span class="text-xs font-medium text-gray-600">{{ $item['status'] }}</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $item['count'] }}</p>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 py-8">
                Заказов пока нет
            </div>
        @endforelse
    </div>
</div>

<!-- Recent Orders & Top Services -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Последние заказы</h2>
        <div class="space-y-3">
            @forelse($recentOrders as $order)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-700 font-semibold flex-shrink-0 text-sm">
                            {{ substr($order->applicant->first_name ?? 'A', 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 truncate text-sm">
                                {{ $order->applicant->first_name }} {{ $order->applicant->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        <p class="font-bold text-gray-900 text-sm">{{ number_format($order->price, 0) }} ₸</p>
                        @if($order->status)
                            <span class="inline-block px-2 py-0.5 text-xs rounded-full text-white" style="background-color: {{ $order->status->color }}">
                                {{ $order->status->name }}
                            </span>
                        @else
                            <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-gray-400 text-white">
                                Без статуса
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    Заказов пока нет
                </div>
            @endforelse
        </div>
        @if($recentOrders->count() > 0)
            <a href="{{ route('manager.orders.index') }}" class="block mt-4 text-center text-xs text-gray-700 hover:text-gray-900 font-medium">
                Посмотреть все заказы →
            </a>
        @endif
    </div>

    <!-- Top Services -->
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Популярные услуги</h2>
        <div class="space-y-3">
            @forelse($topServices as $service)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 truncate text-sm">{{ $service->name }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($service->price, 0) }} ₸</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold text-gray-900">{{ $service->orders_count }}</span>
                            <span class="text-xs text-gray-500">заказов</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    Услуг пока нет
                </div>
            @endforelse
        </div>
        @if($topServices->count() > 0)
            <a href="{{ route('manager.services.index') }}" class="block mt-4 text-center text-xs text-gray-700 hover:text-gray-900 font-medium">
                Посмотреть все услуги →
            </a>
        @endif
    </div>
</div>

<!-- Active Chats -->
<div class="bg-white rounded-lg border border-gray-200 p-5">
    <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Активные чаты</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($activeChats as $chat)
            <a href="{{ route('manager.chat.show', $chat->id) }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-700 font-semibold flex-shrink-0">
                        {{ substr($chat->applicant->first_name ?? 'A', 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-gray-900 truncate text-sm">
                            {{ $chat->applicant->first_name }} {{ $chat->applicant->last_name }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $chat->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if($chat->messages->first())
                    <p class="text-xs text-gray-600 line-clamp-2">
                        {{ $chat->messages->first()->content ?? 'Файл' }}
                    </p>
                @endif
            </a>
        @empty
            <div class="col-span-full text-center text-gray-500 py-8">
                Активных чатов пока нет
            </div>
        @endforelse
    </div>
    @if($activeChats->count() > 0)
        <a href="{{ route('manager.chat.index') }}" class="block mt-4 text-center text-xs text-gray-700 hover:text-gray-900 font-medium">
            Посмотреть все чаты →
        </a>
    @endif
</div>

@endif

@endsection

@push('scripts')
@if($currentOrganization)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Orders Timeline Chart
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        const ordersData = @json($timeline);
        const dates = Object.keys(ordersData);
        const values = Object.values(ordersData);
        
        new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: dates.map(d => new Date(d).toLocaleDateString('ru', { month: 'short', day: 'numeric' })),
                datasets: [{
                    label: 'Заказы',
                    data: values,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // Revenue Timeline Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueData = @json($revenue);
        const dates = Object.keys(revenueData);
        const values = Object.values(revenueData);
        
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: dates.map(d => new Date(d).toLocaleDateString('ru', { month: 'short', day: 'numeric' })),
                datasets: [{
                    label: 'Выручка (₸)',
                    data: values,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('ru') + ' ₸';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endif
@endpush
