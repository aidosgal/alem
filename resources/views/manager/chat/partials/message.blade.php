<div class="flex {{ $message->isFromManager() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
    <div class="max-w-xl {{ $message->isFromManager() ? 'bg-[#EFFE6D]' : 'bg-white' }} rounded-lg p-3 shadow-sm">
        @if($message->isFromApplicant())
            <p class="text-xs font-medium text-gray-600 mb-1">{{ $message->sender_name }}</p>
        @endif

        @if($message->replyTo)
            <div class="mb-2 p-2 bg-gray-100 rounded border-l-2 border-gray-400">
                <p class="text-xs text-gray-600">{{ $message->replyTo->sender_name }}</p>
                <p class="text-xs text-gray-700 truncate">{{ Str::limit($message->replyTo->content, 50) }}</p>
            </div>
        @endif

        @if($message->type === 'image')
            <img src="{{ Storage::url($message->file_path) }}" 
                 alt="{{ $message->file_name }}" 
                 class="max-w-xs rounded-lg mb-2 cursor-pointer"
                 onclick="window.open('{{ Storage::url($message->file_path) }}', '_blank')">
        @elseif($message->type === 'file')
            <a href="{{ route('manager.chat.downloadFile', $message->id) }}" 
               class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition-colors">
                <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $message->file_name }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($message->file_size / 1024, 2) }} KB</p>
                </div>
            </a>
        @elseif($message->type === 'order')
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2">
                <p class="text-sm font-medium text-blue-900">📋 {{ $message->metadata['order_title'] ?? 'Заказ' }}</p>
                @if(isset($message->metadata['order_price']))
                    <p class="text-xs text-blue-700">Цена: ${{ number_format($message->metadata['order_price'], 2) }}</p>
                @endif
            </div>
        @endif

        @if($message->content)
            <p class="text-sm text-gray-900 whitespace-pre-wrap break-words">{{ $message->content }}</p>
        @endif

        <div class="flex items-center justify-between mt-1">
            <p class="text-xs text-gray-500">{{ $message->created_at->format('H:i') }}</p>
            @if($message->isFromManager() && $message->read_at)
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            @endif
        </div>
    </div>
</div>
