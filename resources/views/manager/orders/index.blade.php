@extends('layouts.manager')

@section('title', 'Заказы')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Заказы</h1>
    <p class="text-gray-600 mt-1">Управляйте статусами заказов с помощью Kanban доски</p>
</div>

@if(empty($kanban))
    <div class="p-12 text-center bg-white rounded-2xl border border-gray-200">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Настройте статусы</h3>
        <p class="text-gray-600 mb-6">Сначала создайте статусы для управления заказами</p>
    </div>
@else
    <div class="flex gap-4 overflow-x-auto pb-4" id="kanban-board">
        @foreach($kanban as $column)
            <div class="flex-shrink-0 w-80 bg-gray-50 rounded-xl p-4" data-status-id="{{ $column['status']->id }}">
                <!-- Column Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $column['status']->color }}"></div>
                        <h3 class="font-semibold text-gray-900">{{ $column['status']->name }}</h3>
                        <span class="px-2 py-0.5 text-xs font-medium text-gray-600 bg-gray-200 rounded-full">
                            {{ $column['orders']->count() }}
                        </span>
                    </div>
                </div>

                <!-- Cards Container -->
                <div class="space-y-3 min-h-[200px] kanban-column" data-status-id="{{ $column['status']->id }}">
                    @forelse($column['orders'] as $order)
                        <div class="p-4 bg-white rounded-lg border border-gray-200 cursor-move kanban-card hover:shadow-md transition-shadow"
                             draggable="true"
                             data-order-id="{{ $order->id }}">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $order->title }}</h4>
                            
                            @if($order->description)
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $order->description }}</p>
                            @endif

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>{{ $order->applicant->user->email ?? 'N/A' }}</span>
                                </div>
                            </div>

                            @if($order->price)
                                <div class="mt-2 pt-2 border-t border-gray-100">
                                    <span class="text-sm font-semibold text-[#319885]">{{ number_format($order->price, 2) }} EUR</span>
                                </div>
                            @endif

                            @if($order->deadline_at)
                                <div class="mt-2 flex items-center gap-1 text-xs text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $order->deadline_at->format('d.m.Y') }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-gray-400">
                            Перетащите заказы сюда
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kanbanBoard = document.getElementById('kanban-board');
    if (!kanbanBoard) return;

    let draggedCard = null;

    // Add drag event listeners to all cards
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });

    // Add drop zone event listeners to all columns
    document.querySelectorAll('.kanban-column').forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('drop', handleDrop);
        column.addEventListener('dragleave', handleDragLeave);
    });

    function handleDragStart(e) {
        draggedCard = this;
        this.style.opacity = '0.4';
        e.dataTransfer.effectAllowed = 'move';
    }

    function handleDragEnd(e) {
        this.style.opacity = '1';
        
        // Remove all drag-over classes
        document.querySelectorAll('.kanban-column').forEach(col => {
            col.classList.remove('bg-blue-50', 'border-2', 'border-blue-300', 'border-dashed');
        });
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        
        this.classList.add('bg-blue-50', 'border-2', 'border-blue-300', 'border-dashed');
        e.dataTransfer.dropEffect = 'move';
        
        return false;
    }

    function handleDragLeave(e) {
        this.classList.remove('bg-blue-50', 'border-2', 'border-blue-300', 'border-dashed');
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }

        this.classList.remove('bg-blue-50', 'border-2', 'border-blue-300', 'border-dashed');

        if (draggedCard) {
            const orderId = draggedCard.dataset.orderId;
            const newStatusId = this.dataset.statusId;
            const oldColumn = draggedCard.closest('.kanban-column');

            // Move card to new column
            this.insertBefore(draggedCard, this.querySelector('.p-4.text-center'));
            
            // Update counts
            updateColumnCounts();

            // Send update to server
            updateOrderStatus(orderId, newStatusId, oldColumn);
        }

        return false;
    }

    function updateColumnCounts() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const count = column.querySelectorAll('.kanban-card').length;
            const badge = column.closest('[data-status-id]').querySelector('.rounded-full');
            if (badge) {
                badge.textContent = count;
            }
        });
    }

    function updateOrderStatus(orderId, newStatusId, oldColumn) {
        fetch(`/manager/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status_id: newStatusId })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Revert on error
                oldColumn.insertBefore(draggedCard, oldColumn.firstChild);
                updateColumnCounts();
                alert('Ошибка: ' + (data.error || 'Не удалось обновить статус'));
            }
        })
        .catch(error => {
            // Revert on error
            oldColumn.insertBefore(draggedCard, oldColumn.firstChild);
            updateColumnCounts();
            alert('Ошибка: ' + error.message);
        });
    }
});
</script>
@endpush
@endsection
