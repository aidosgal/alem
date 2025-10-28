@extends('layouts.manager')

@section('title', 'Чат')

@section('content')
<div class="h-[calc(100vh-8rem)] flex flex-col max-w-7xl mx-auto">
    <!-- Chat header -->
    <div class="bg-white border border-gray-200 rounded-t-lg p-4 flex items-center justify-between shadow-sm backdrop-blur-sm bg-white/95 sticky top-0 z-10">
        <div class="flex items-center">
            <a href="{{ route('manager.chat.index') }}" class="mr-4 text-gray-600 hover:text-gray-900 transition-all duration-300 hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="w-10 h-10 bg-gradient-to-br from-[#319885] to-[#2a8070] rounded-full flex items-center justify-center text-white font-semibold avatar shadow-md">
                {{ strtoupper(substr($chat->applicant->user->name ?? 'A', 0, 1)) }}
            </div>
            <div class="ml-3">
                <h2 class="text-lg font-semibold text-gray-900">{{ $chat->applicant->user->name ?? 'Аппликант' }}</h2>
                <div class="flex items-center gap-2">
                    <div id="typing-indicator" class="typing-indicator" style="display: none;">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                    <div class="connection-status text-sm" id="connection-status">
                        <span class="status-dot disconnected"></span>
                        <span class="text-gray-500">Подключение...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            @if($chat->order)
                <a href="{{ route('manager.orders.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all duration-300 hover-lift">
                    Заказ: {{ $chat->order->title }}
                </a>
            @else
                <button onclick="openOrderModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-900 bg-[#EFFE6D] rounded-lg hover:bg-[#EFFE6D]/90 transition-all duration-300 btn-ripple shadow-md hover:shadow-lg">
                    Создать заказ
                </button>
            @endif
        </div>
    </div>

    <!-- Messages container -->
    <div id="messages-container" 
         class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 p-4 space-y-4 custom-scrollbar"
         style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"100\" height=\"100\"><text x=\"50%\" y=\"50%\" font-size=\"80\" text-anchor=\"middle\" fill=\"%23e5e7eb\" opacity=\"0.05\">💬</text></svg>')">
        
        <!-- Load more button -->
        <div id="load-more-container" class="text-center" style="display: none;">
            <button onclick="loadMoreMessages()" 
                    class="px-4 py-2 text-sm text-gray-600 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 transition-all duration-300 hover-lift shadow-sm">
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
    <div class="bg-white border border-gray-200 rounded-b-lg p-4 shadow-lg backdrop-blur-sm bg-white/95">
        <!-- File upload area (drag & drop) -->
        <div id="drop-zone" 
             class="border-2 border-dashed border-gray-300 rounded-lg p-4 mb-3 text-center drop-zone transition-all duration-300"
             style="display: none;">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-sm text-gray-600">Перетащите файлы сюда или <span class="text-[#319885] font-medium">выберите</span></p>
        </div>

        <!-- Selected file preview -->
        <div id="file-preview" class="mb-3 message-enter" style="display: none;">
            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-[#319885] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900" id="file-name"></p>
                        <p class="text-xs text-gray-500" id="file-size"></p>
                    </div>
                </div>
                <button onclick="clearFile()" class="text-red-600 hover:text-red-800 transition-all duration-300 hover:scale-110">
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
                    class="flex-shrink-0 px-3 py-2 text-gray-600 hover:text-[#319885] hover:bg-gray-100 rounded-lg transition-all duration-300 hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
            </button>

            <textarea id="message-input" 
                      name="content"
                      rows="1"
                      placeholder="Введите сообщение..."
                      class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent resize-none input-animated transition-all duration-300"
                      onkeydown="handleKeyDown(event)"
                      oninput="handleTyping()"></textarea>

            <button type="submit" 
                    class="flex-shrink-0 px-6 py-2 text-sm font-medium text-gray-900 bg-gradient-to-r from-[#EFFE6D] to-[#f5ff80] rounded-lg hover:from-[#f5ff80] hover:to-[#EFFE6D] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed btn-ripple shadow-md hover:shadow-lg"
                    id="send-button">
                <span class="flex items-center gap-2">
                    <span>Отправить</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </button>
        </form>
    </div>
</div>

<!-- Order creation modal -->
<div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center modal-backdrop backdrop-blur-sm">
    <div class="bg-white rounded-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Создать заказ</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 transition-all duration-300 hover:scale-110">
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent input-animated">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent input-animated resize-none"></textarea>
                </div>

                <!-- Services -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Услуги</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 custom-scrollbar">
                        @foreach($services as $service)
                            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-all duration-300">
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent input-animated">
                    <p class="mt-1 text-xs text-gray-500">Можно изменить независимо от суммы услуг</p>
                </div>

                <!-- Deadline -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Срок выполнения</label>
                    <input type="datetime-local" 
                           name="deadline_at"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#319885] focus:border-transparent input-animated">
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 justify-end">
                    <button type="button" 
                            onclick="closeOrderModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-300 hover-lift">
                        Отмена
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-gray-900 bg-[#EFFE6D] rounded-lg hover:bg-[#EFFE6D]/90 transition-all duration-300 btn-ripple shadow-md hover:shadow-lg">
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
    const statusDot = statusEl.querySelector('.status-dot');
    const statusText = statusEl.querySelector('span:last-child');
    
    if (connected) {
        statusDot.classList.remove('disconnected');
        statusDot.classList.add('connected');
        statusText.textContent = 'Подключено';
        statusText.classList.remove('text-red-600');
        statusText.classList.add('text-green-600');
    } else {
        statusDot.classList.remove('connected');
        statusDot.classList.add('disconnected');
        statusText.textContent = customMessage || 'Не подключено';
        statusText.classList.remove('text-green-600');
        statusText.classList.add('text-red-600');
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
        dropZone.classList.add('drag-over');
    }, false);
});

['dragleave', 'drop'].forEach(eventName => {
    messagesContainer.addEventListener(eventName, () => {
        dropZone.classList.remove('drag-over');
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
    messageDiv.className = `flex ${isOwnMessage ? 'justify-end message-enter-right' : 'justify-start message-enter-left'}`;
    messageDiv.innerHTML = createMessageHTML(messageData, isOwnMessage);
    
    messagesList.appendChild(messageDiv);
    
    // Trigger animation
    setTimeout(() => {
        messageDiv.classList.remove('message-enter-right', 'message-enter-left');
    }, 400);
}

function createMessageHTML(msg, isOwn) {
    const bgClass = isOwn ? 'message-bubble message-bubble-right' : 'message-bubble message-bubble-left';
    const timeStr = new Date(msg.created_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
    
    let contentHTML = '';
    
    if (msg.type === 'image') {
        contentHTML = `<img src="/storage/${msg.file_path}" alt="${msg.file_name}" class="max-w-xs rounded-lg mb-2 image-preview">`;
    } else if (msg.type === 'file') {
        contentHTML = `
            <a href="/manager/chat/message/${msg.id}/download" class="flex items-center p-3 bg-gray-50/50 rounded-lg mb-2 hover:bg-gray-100/80 transition-all duration-300">
                <svg class="w-8 h-8 text-[#319885] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2 transition-all duration-300 hover:shadow-md">
                <p class="text-sm font-medium text-blue-900">📋 ${msg.metadata.order_title}</p>
                <p class="text-xs text-blue-700">Цена: $${msg.metadata.order_price}</p>
            </div>
        `;
    }
    
    if (msg.content) {
        contentHTML += `<p class="text-sm text-gray-900 whitespace-pre-wrap break-words">${escapeHtml(msg.content)}</p>`;
    }
    
    return `
        <div class="max-w-xl ${bgClass} rounded-2xl p-3 shadow-sm">
            ${!isOwn ? `<p class="text-xs font-medium text-gray-600 mb-1">${escapeHtml(msg.sender.name)}</p>` : ''}
            ${contentHTML}
            <p class="text-xs text-gray-500 mt-1 flex items-center justify-between">
                <span>${timeStr}</span>
                ${isOwn ? '<svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : ''}
            </p>
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
    const modal = document.getElementById('order-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'modal-enter');
    setTimeout(() => modal.classList.remove('modal-enter'), 300);
}

function closeOrderModal() {
    const modal = document.getElementById('order-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
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
