@extends('layouts.manager')

@section('title', 'Чат')

@section('content')
<div class="h-[calc(100vh-8rem)] flex flex-col max-w-7xl mx-auto">
    <!-- Chat header -->
    <div class="bg-white border border-gray-200 rounded-t-lg p-4 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('manager.chat.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="w-10 h-10 bg-[#319885] rounded-full flex items-center justify-center text-white font-semibold">
                {{ strtoupper(substr($chat->applicant->user->name ?? 'A', 0, 1)) }}
            </div>
            <div class="ml-3">
                <h2 class="text-lg font-semibold text-gray-900">{{ $chat->applicant->user->name ?? 'Аппликант' }}</h2>
                <p class="text-sm text-gray-500" id="typing-indicator" style="display: none;">Печатает...</p>
                <p class="text-sm text-gray-500" id="connection-status">●<span class="ml-1">Подключение...</span></p>
            </div>
        </div>
        <div class="flex gap-2">
            @if($chat->order)
                <a href="{{ route('manager.orders.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Заказ: {{ $chat->order->title }}
                </a>
            @else
                <button onclick="openOrderModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-900 bg-[#EFFE6D] rounded-lg hover:bg-[#EFFE6D]/90 transition-colors">
                    Создать заказ
                </button>
            @endif
        </div>
    </div>

    <!-- Messages container -->
    <div id="messages-container" 
         class="flex-1 overflow-y-auto bg-gray-50 p-4 space-y-4"
         style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"100\" height=\"100\"><text x=\"50%\" y=\"50%\" font-size=\"80\" text-anchor=\"middle\" fill=\"%23f3f4f6\" opacity=\"0.1\">💬</text></svg>')">
        
        <!-- Load more button -->
        <div id="load-more-container" class="text-center" style="display: none;">
            <button onclick="loadMoreMessages()" 
                    class="px-4 py-2 text-sm text-gray-600 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">
                Загрузить еще
            </button>
        </div>

        <!-- Messages will be inserted here -->
        <div id="messages-list">
            @foreach($chat->messages as $message)
                @include('manager.chat.partials.message', ['message' => $message])
            @endforeach
        </div>

        <!-- Loading indicator -->
        <div id="loading-indicator" class="text-center py-4" style="display: none;">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#319885]"></div>
        </div>
    </div>

    <!-- Message input -->
    <div class="bg-white border border-gray-200 rounded-b-lg p-4">
        <!-- File upload area (drag & drop) -->
        <div id="drop-zone" 
             class="border-2 border-dashed border-gray-300 rounded-lg p-4 mb-3 text-center transition-colors"
             style="display: none;">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-sm text-gray-600">Перетащите файлы сюда или <span class="text-[#319885] font-medium">выберите</span></p>
        </div>

        <!-- Selected file preview -->
        <div id="file-preview" class="mb-3" style="display: none;">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900" id="file-name"></p>
                        <p class="text-xs text-gray-500" id="file-size"></p>
                    </div>
                </div>
                <button onclick="clearFile()" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form id="message-form" class="flex gap-2">
            @csrf
            <input type="file" id="file-input" class="hidden" onchange="handleFileSelect(event)">
            
            <button type="button" 
                    onclick="document.getElementById('file-input').click()"
                    class="flex-shrink-0 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
            </button>

            <textarea id="message-input" 
                      name="content"
                      rows="1"
                      placeholder="Введите сообщение..."
                      class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent resize-none"
                      onkeydown="handleKeyDown(event)"
                      oninput="handleTyping()"></textarea>

            <button type="submit" 
                    class="flex-shrink-0 px-6 py-2 text-sm font-medium text-gray-900 bg-[#EFFE6D] rounded-lg hover:bg-[#EFFE6D]/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    id="send-button">
                Отправить
            </button>
        </form>
    </div>
</div>

<!-- Order creation modal -->
<div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Создать заказ</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="order-form">
                @csrf
                <!-- Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Название заказа <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent"></textarea>
                </div>

                <!-- Services -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Услуги</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @foreach($services as $service)
                            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" 
                                       name="service_ids[]" 
                                       value="{{ $service->id }}"
                                       data-price="{{ $service->price }}"
                                       onchange="calculateTotalPrice()"
                                       class="w-4 h-4 text-[#319885] border-gray-300 rounded focus:ring-[#319885]">
                                <span class="ml-3 text-sm text-gray-900">{{ $service->title }}</span>
                                <span class="ml-auto text-sm text-gray-600">${{ number_format($service->price, 2) }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Сумма услуг: $<span id="services-total">0.00</span></p>
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Цена заказа <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="price" 
                           id="order-price"
                           step="0.01"
                           min="0"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Можно изменить независимо от суммы услуг</p>
                </div>

                <!-- Deadline -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Срок выполнения</label>
                    <input type="datetime-local" 
                           name="deadline_at"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent">
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 justify-end">
                    <button type="button" 
                            onclick="closeOrderModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Отмена
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-gray-900 bg-[#EFFE6D] rounded-lg hover:bg-[#EFFE6D]/90">
                        Создать заказ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const chatId = '{{ $chat->id }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let channel = null;
let selectedFile = null;
let reconnectAttempts = 0;
let maxReconnectAttempts = 5;
let typingTimeout = null;
let isTyping = false;

// Initialize Echo connection
function initializeEcho() {
    try {
        // Subscribe to chat channel
        channel = window.Echo.private(`chat.${chatId}`)
            .listen('.message.sent', (e) => {
                console.log('New message received:', e);
                appendMessage(e);
                scrollToBottom();
            })
            .error((error) => {
                console.error('Channel error:', error);
                updateConnectionStatus(false);
                attemptReconnect();
            });

        // Connection successful
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('WebSocket connected');
            updateConnectionStatus(true);
            reconnectAttempts = 0;
        });

        // Connection lost
        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            console.log('WebSocket disconnected');
            updateConnectionStatus(false);
        });

        // Connection error
        window.Echo.connector.pusher.connection.bind('error', (error) => {
            console.error('WebSocket error:', error);
            updateConnectionStatus(false);
            attemptReconnect();
        });

    } catch (error) {
        console.error('Failed to initialize Echo:', error);
        updateConnectionStatus(false);
        attemptReconnect();
    }
}

// Attempt to reconnect
function attemptReconnect() {
    if (reconnectAttempts < maxReconnectAttempts) {
        reconnectAttempts++;
        const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000);
        console.log(`Reconnecting in ${delay}ms (attempt ${reconnectAttempts}/${maxReconnectAttempts})`);
        
        setTimeout(() => {
            console.log('Attempting to reconnect...');
            if (channel) {
                window.Echo.leave(`chat.${chatId}`);
            }
            initializeEcho();
        }, delay);
    } else {
        console.error('Max reconnection attempts reached');
        updateConnectionStatus(false, 'Не удалось подключиться. Обновите страницу.');
    }
}

// Update connection status indicator
function updateConnectionStatus(connected, customMessage = null) {
    const statusEl = document.getElementById('connection-status');
    if (connected) {
        statusEl.innerHTML = '<span class="text-green-600">●</span><span class="ml-1 text-green-600">Подключено</span>';
    } else {
        statusEl.innerHTML = '<span class="text-red-600">●</span><span class="ml-1 text-red-600">' + (customMessage || 'Не подключено') + '</span>';
    }
}

// Handle form submission
document.getElementById('message-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const input = document.getElementById('message-input');
    const content = input.value.trim();
    const sendButton = document.getElementById('send-button');
    
    if (!content && !selectedFile) return;
    
    sendButton.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('content', content);
        if (selectedFile) {
            formData.append('file', selectedFile);
        }
        
        const response = await fetch(`/manager/chat/${chatId}/message`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            input.style.height = 'auto';
            clearFile();
            
            // Message will be added via WebSocket
            // But add it immediately for better UX
            if (!channel) {
                appendMessage(data.message);
            }
            scrollToBottom();
        } else {
            alert(data.error || 'Ошибка отправки сообщения');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Ошибка отправки сообщения');
    } finally {
        sendButton.disabled = false;
    }
});

// Handle keyboard shortcuts
function handleKeyDown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('message-form').dispatchEvent(new Event('submit'));
    }
}

// Auto-resize textarea
document.getElementById('message-input').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Handle typing indicator
function handleTyping() {
    // TODO: Implement typing indicator broadcast
}

// File handling
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            alert('Файл слишком большой. Максимум 10 МБ.');
            return;
        }
        selectedFile = file;
        showFilePreview(file);
    }
}

function showFilePreview(file) {
    document.getElementById('file-preview').style.display = 'block';
    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-size').textContent = formatFileSize(file.size);
}

function clearFile() {
    selectedFile = null;
    document.getElementById('file-input').value = '';
    document.getElementById('file-preview').style.display = 'none';
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

// Drag and drop
const dropZone = document.getElementById('drop-zone');
const messagesContainer = document.getElementById('messages-container');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    messagesContainer.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    messagesContainer.addEventListener(eventName, () => {
        dropZone.style.display = 'block';
        dropZone.classList.add('border-[#319885]', 'bg-[#319885]/5');
    }, false);
});

['dragleave', 'drop'].forEach(eventName => {
    messagesContainer.addEventListener(eventName, () => {
        dropZone.classList.remove('border-[#319885]', 'bg-[#319885]/5');
        setTimeout(() => {
            if (!messagesContainer.matches(':hover')) {
                dropZone.style.display = 'none';
            }
        }, 100);
    }, false);
});

messagesContainer.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files.length > 0) {
        selectedFile = files[0];
        showFilePreview(files[0]);
    }
}, false);

// Append new message to chat
function appendMessage(messageData) {
    const messagesList = document.getElementById('messages-list');
    const isOwnMessage = messageData.sender.type === 'manager';
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'}`;
    messageDiv.innerHTML = createMessageHTML(messageData, isOwnMessage);
    
    messagesList.appendChild(messageDiv);
}

function createMessageHTML(msg, isOwn) {
    const bgClass = isOwn ? 'bg-[#EFFE6D]' : 'bg-white';
    const timeStr = new Date(msg.created_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
    
    let contentHTML = '';
    
    if (msg.type === 'image') {
        contentHTML = `<img src="/storage/${msg.file_path}" alt="${msg.file_name}" class="max-w-xs rounded-lg mb-2">`;
    } else if (msg.type === 'file') {
        contentHTML = `
            <a href="/manager/chat/message/${msg.id}/download" class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100">
                <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium">${msg.file_name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(msg.file_size)}</p>
                </div>
            </a>
        `;
    } else if (msg.type === 'order') {
        contentHTML = `
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2">
                <p class="text-sm font-medium text-blue-900">📋 ${msg.metadata.order_title}</p>
                <p class="text-xs text-blue-700">Цена: $${msg.metadata.order_price}</p>
            </div>
        `;
    }
    
    if (msg.content) {
        contentHTML += `<p class="text-sm text-gray-900 whitespace-pre-wrap break-words">${escapeHtml(msg.content)}</p>`;
    }
    
    return `
        <div class="max-w-xl ${bgClass} rounded-lg p-3 shadow-sm">
            ${!isOwn ? `<p class="text-xs font-medium text-gray-600 mb-1">${escapeHtml(msg.sender.name)}</p>` : ''}
            ${contentHTML}
            <p class="text-xs text-gray-500 mt-1">${timeStr}</p>
        </div>
    `;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
}

// Load more messages
async function loadMoreMessages() {
    const firstMessage = document.querySelector('[data-message-id]');
    if (!firstMessage) return;
    
    const beforeId = firstMessage.dataset.messageId;
    const loadingIndicator = document.getElementById('loading-indicator');
    loadingIndicator.style.display = 'block';
    
    try {
        const response = await fetch(`/manager/chat/${chatId}/messages?before_id=${beforeId}`);
        const data = await response.json();
        
        if (data.messages && data.messages.length > 0) {
            const messagesList = document.getElementById('messages-list');
            const oldScrollHeight = messagesContainer.scrollHeight;
            
            data.messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${msg.sender.type === 'manager' ? 'justify-end' : 'justify-start'}`;
                messageDiv.innerHTML = createMessageHTML(msg, msg.sender.type === 'manager');
                messagesList.insertBefore(messageDiv, messagesList.firstChild);
            });
            
            // Maintain scroll position
            messagesContainer.scrollTop = messagesContainer.scrollHeight - oldScrollHeight;
            
            if (!data.has_more) {
                document.getElementById('load-more-container').style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    } finally {
        loadingIndicator.style.display = 'none';
    }
}

// Order modal functions
function openOrderModal() {
    document.getElementById('order-modal').classList.remove('hidden');
    document.getElementById('order-modal').classList.add('flex');
}

function closeOrderModal() {
    document.getElementById('order-modal').classList.remove('flex');
    document.getElementById('order-modal').classList.add('hidden');
}

function calculateTotalPrice() {
    const checkboxes = document.querySelectorAll('input[name="service_ids[]"]:checked');
    let total = 0;
    checkboxes.forEach(cb => {
        total += parseFloat(cb.dataset.price);
    });
    document.getElementById('services-total').textContent = total.toFixed(2);
    document.getElementById('order-price').value = total.toFixed(2);
}

// Handle order form submission
document.getElementById('order-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(`/manager/chat/${chatId}/order`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeOrderModal();
            location.reload(); // Reload to show updated chat with order
        } else {
            alert(data.error || 'Ошибка создания заказа');
        }
    } catch (error) {
        console.error('Error creating order:', error);
        alert('Ошибка создания заказа');
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeEcho();
    scrollToBottom();
    
    // Show load more button if there are messages
    const messagesList = document.getElementById('messages-list');
    if (messagesList.children.length >= 50) {
        document.getElementById('load-more-container').style.display = 'block';
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (channel) {
        window.Echo.leave(`chat.${chatId}`);
    }
});
</script>
@endpush
@endsection
