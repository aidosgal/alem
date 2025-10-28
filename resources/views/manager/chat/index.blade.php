@extends('layouts.manager')

@section('title', 'Чаты')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 message-enter">
        <h1 class="text-3xl font-bold text-gray-900">Чаты с аппликантами</h1>
        <p class="mt-2 text-sm text-gray-600">Общайтесь с кандидатами и создавайте заказы</p>
    </div>

    @if($chats->isEmpty())
        <!-- Empty state -->
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm hover-lift">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4 transition-transform duration-500 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Нет активных чатов</h3>
            <p class="text-gray-600">Чаты появятся, когда кандидаты начнут с вами общаться</p>
        </div>
    @else
        <!-- Chats list -->
        <div class="bg-white rounded-2xl border border-gray-200 divide-y divide-gray-200 shadow-sm overflow-hidden">
            @foreach($chats as $chat)
                <a href="{{ route('manager.chat.show', $chat->id) }}" 
                   class="flex items-center p-4 chat-item transition-all duration-300">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#319885] to-[#2a8070] rounded-full flex items-center justify-center text-white font-semibold text-lg avatar shadow-md">
                            {{ strtoupper(substr($chat->applicant->user->name ?? 'A', 0, 1)) }}
                        </div>
                    </div>

                    <!-- Chat info -->
                    <div class="flex-1 min-w-0 ml-4">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-sm font-semibold text-gray-900 truncate transition-colors duration-300">
                                {{ $chat->applicant->user->name ?? 'Аппликант' }}
                            </h3>
                            @if($chat->last_message_at)
                                <span class="text-xs text-gray-500 ml-2 transition-colors duration-300">
                                    {{ $chat->last_message_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                        
                        @if($chat->lastMessage)
                            <p class="text-sm text-gray-600 truncate transition-colors duration-300">
                                @if($chat->lastMessage->type === 'image')
                                    📷 Изображение
                                @elseif($chat->lastMessage->type === 'file')
                                    📎 Файл
                                @elseif($chat->lastMessage->type === 'order')
                                    📋 Заказ создан
                                @else
                                    {{ $chat->lastMessage->content }}
                                @endif
                            </p>
                        @endif

                        @if($chat->order)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium status-badge"
                                      style="background-color: {{ $chat->order->orderStatus->color }}20; color: {{ $chat->order->orderStatus->color }}">
                                    {{ $chat->order->orderStatus->name }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Unread badge -->
                    @if($chat->unread_count > 0)
                        <div class="flex-shrink-0 ml-4">
                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-gradient-to-br from-[#319885] to-[#2a8070] rounded-full unread-badge shadow-md">
                                {{ $chat->unread_count }}
                            </span>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
