@extends('layouts.user')

@section('title', 'Тикет #' . $ticket->id . ' - Cliently')
@section('page-title', 'Тикет #' . $ticket->id)

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Тикеты', 'url' => route('tickets.index')],
        ['title' => 'Тикет #' . $ticket->id, 'url' => null]
    ]" />
@endpush

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="space-y-6">
        <!-- Информация о тикете -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $ticket->title }}</h2>
                        <span class="px-3 py-1.5 text-sm font-medium rounded-full
                            {{ $ticket->status === 'new' ? 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400' : '' }}
                            {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400' : '' }}
                            {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' : '' }}
                            {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400' : '' }}">
                            {{ $ticket->status === 'new' ? 'Новый' : ($ticket->status === 'open' ? 'В работе' : ($ticket->status === 'resolved' ? 'Решен' : 'Закрыт')) }}
                        </span>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-600 dark:text-slate-400 mb-4">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-calendar text-slate-400"></i>
                            Создан {{ $ticket->created_at->format('d.m.Y в H:i') }}
                        </span>
                        @if($ticket->category)
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-tag text-slate-400"></i>
                                {{ $ticket->category->name }}
                            </span>
                        @endif
                        @if($ticket->assignedUser)
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-user text-slate-400"></i>
                                Назначен: {{ $ticket->assignedUser->name }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="ml-4">
                    <a href="{{ route('tickets.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-arrow-left"></i>
                        Назад к списку
                    </a>
                </div>
            </div>

            <div class="prose dark:prose-invert max-w-none mb-6">
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $ticket->description }}</p>
                </div>
            </div>

            @if($ticket->attachments->count() > 0)
                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-paperclip text-slate-400"></i>
                        Прикрепленные файлы ({{ $ticket->attachments->count() }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($ticket->attachments as $attachment)
                            <a href="{{ $attachment->url }}" target="_blank" 
                               class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors group">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                    <i class="fa-solid fa-file text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $attachment->file_name }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $attachment->formatted_size }}</p>
                                </div>
                                <i class="fa-solid fa-download text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Комментарии -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-comments text-indigo-600 dark:text-indigo-400"></i>
                Ответы
                @if($ticket->comments->count() > 0)
                    <span class="text-sm font-normal text-slate-500 dark:text-slate-400">
                        ({{ $ticket->comments->count() }})
                    </span>
                @endif
            </h3>

            @if($ticket->comments->count() > 0)
                <div class="space-y-4 mb-6">
                    @foreach($ticket->comments as $comment)
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $comment->getAuthorName() }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $comment->created_at->format('d.m.Y в H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap mb-3">{{ $comment->content }}</p>
                            
                            @if($comment->attachments->count() > 0)
                                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($comment->attachments as $attachment)
                                            <a href="{{ $attachment->url }}" target="_blank"
                                               class="inline-flex items-center gap-2 px-2 py-1 text-xs bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                                <i class="fa-solid fa-file text-slate-400"></i>
                                                {{ $attachment->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 mb-6">
                    <i class="fa-solid fa-comments text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                    <p class="text-slate-500 dark:text-slate-400">Пока нет комментариев</p>
                </div>
            @endif

            <!-- Форма добавления комментария -->
            <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                <form method="POST" action="{{ route('tickets.comments.store', $ticket->id) }}" enctype="multipart/form-data" 
                    x-data="{ submitting: false }"
                    @submit="submitting = true"
                    class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Добавить комментарий
                        </label>
                        <textarea name="content" rows="4" required
                            placeholder="Введите ваш комментарий..."
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"></textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div x-data="fileUpload()">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-paperclip mr-2 text-slate-400"></i>
                            Прикрепленные файлы
                        </label>
                        <div 
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)"
                            :class="isDragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : ''"
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-lg hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-2 transition-transform" :class="isDragging ? 'scale-110 text-indigo-500' : ''"></i>
                                <div class="flex text-sm text-slate-600 dark:text-slate-400">
                                    <label for="attachments" class="relative cursor-pointer rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Выберите файлы</span>
                                        <input id="attachments" name="attachments[]" type="file" multiple accept="image/*,.pdf,.doc,.docx,.txt" class="sr-only" @change="handleFiles($event)">
                                    </label>
                                    <p class="pl-1">или перетащите сюда</p>
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG, PDF, DOC, DOCX, TXT до 10 МБ</p>
                            </div>
                        </div>
                        
                        <!-- Список выбранных файлов -->
                        <div x-show="selectedFiles.length > 0" x-cloak class="mt-4 space-y-2">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                Выбрано файлов: <span x-text="selectedFiles.length"></span>
                            </p>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-for="(file, index) in selectedFiles" :key="index">
                                    <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <i class="fa-solid fa-file text-indigo-600 dark:text-indigo-400 flex-shrink-0"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white truncate" x-text="file.name"></p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="formatFileSize(file.size)"></p>
                                        </div>
                                        <button type="button" @click="removeFile(index)" 
                                            class="flex-shrink-0 p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit"
                            :disabled="submitting"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fa-solid" :class="submitting ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                            <span x-text="submitting ? 'Отправка...' : 'Отправить'"></span>
                        </button>
                        <a href="{{ route('tickets.index') }}"
                            class="px-6 py-2.5 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg font-medium transition-colors">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function fileUpload() {
            return {
                selectedFiles: [],
                isDragging: false,
                
                handleFiles(event) {
                    const files = Array.from(event.target.files);
                    this.addFiles(files);
                },
                
                handleDrop(event) {
                    this.isDragging = false;
                    const files = Array.from(event.dataTransfer.files);
                    this.addFiles(files);
                    // Обновляем input для отправки формы
                    const input = document.getElementById('attachments');
                    const dataTransfer = new DataTransfer();
                    this.selectedFiles.forEach(file => {
                        dataTransfer.items.add(file);
                    });
                    input.files = dataTransfer.files;
                },
                
                addFiles(files) {
                    const maxSize = 10 * 1024 * 1024; // 10 МБ
                    files.forEach(file => {
                        if (file.size > maxSize) {
                            alert(`Файл "${file.name}" слишком большой. Максимальный размер: 10 МБ`);
                            return;
                        }
                        // Проверяем, не добавлен ли уже этот файл
                        if (!this.selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
                            this.selectedFiles.push(file);
                        }
                    });
                    // Обновляем input
                    this.updateInput();
                },
                
                removeFile(index) {
                    const fileToRemove = this.selectedFiles[index];
                    this.selectedFiles.splice(index, 1);
                    this.updateInput();
                },
                
                updateInput() {
                    const input = document.getElementById('attachments');
                    const dataTransfer = new DataTransfer();
                    this.selectedFiles.forEach(file => {
                        dataTransfer.items.add(file);
                    });
                    input.files = dataTransfer.files;
                },
                
                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                }
            }
        }
    </script>
    @endpush
        </div>
    </div>
@endsection
