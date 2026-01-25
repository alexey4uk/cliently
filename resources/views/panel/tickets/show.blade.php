@extends('layouts.panel')

@section('title', 'Тикет #' . $ticket->id)

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Тикет #{{ $ticket->id }}</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">{{ $ticket->title }}</p>
            </div>
            <a href="{{ route('panel.tickets') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                Назад к списку
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Основной контент -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Информация о тикете -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">{{ $ticket->title }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-600 dark:text-slate-400 mb-4">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar text-slate-400"></i>
                                    {{ $ticket->created_at->format('d.m.Y в H:i') }}
                                </span>
                                @if($ticket->category)
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-tag text-slate-400"></i>
                                        {{ $ticket->category->name }}
                                    </span>
                                @endif
                                @if($ticket->client)
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-user text-slate-400"></i>
                                        {{ $ticket->client->first_name }} {{ $ticket->client->last_name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="px-3 py-1.5 text-sm font-medium rounded-full
                            {{ $ticket->status === 'new' ? 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400' : '' }}
                            {{ $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400' : '' }}
                            {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' : '' }}
                            {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400' : '' }}">
                            {{ $ticket->status === 'new' ? 'Новый' : ($ticket->status === 'in_progress' ? 'В работе' : ($ticket->status === 'resolved' ? 'Решен' : 'Закрыт')) }}
                        </span>
                    </div>

                    <div class="prose dark:prose-invert max-w-none mb-6">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                            <p class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $ticket->description }}</p>
                        </div>
                    </div>

                    @if($ticket->attachments->count() > 0)
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-paperclip text-slate-400"></i>
                                Прикрепленные файлы ({{ $ticket->attachments->count() }})
                            </h4>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($ticket->attachments as $attachment)
                                    <a href="{{ $attachment->url }}" target="_blank"
                                       class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                                        <i class="fa-solid fa-file text-indigo-600 dark:text-indigo-400"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $attachment->file_name }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $attachment->formatted_size }}</p>
                                        </div>
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
                        Комментарии
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
                                        @if($comment->is_internal)
                                            <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400">
                                                Внутренний
                                            </span>
                                        @endif
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

                    <!-- Форма комментария -->
                    @if(auth()->user()->can('panel.tickets.update') || $ticket->assigned_to === auth()->id())
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                        <form method="POST" action="{{ route('panel.tickets.comments.store', $ticket) }}" enctype="multipart/form-data" 
                            x-data="{ submitting: false }"
                            @submit="submitting = true"
                            class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Добавить комментарий</label>
                                <textarea name="content" rows="4" required placeholder="Введите ваш комментарий..."
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"></textarea>
                            </div>
                            <div x-data="fileUploadAdmin()">
                                <label for="attachments_admin" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-paperclip mr-2 text-slate-400"></i>
                                    Прикрепить файлы
                                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">(необязательно)</span>
                                </label>
                                <div class="relative">
                                    <input type="file" 
                                        id="attachments_admin"
                                        name="attachments[]" 
                                        multiple 
                                        accept="image/*,.pdf,.doc,.docx,.txt"
                                        @change="handleFiles($event)"
                                        class="block w-full text-sm text-slate-500 dark:text-slate-400
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-lg file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-500/20 dark:file:text-indigo-300
                                            hover:file:bg-indigo-100 dark:hover:file:bg-indigo-500/30
                                            file:cursor-pointer
                                            cursor-pointer
                                            rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 
                                            focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                </div>
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    Максимальный размер файла: 10 МБ. Поддерживаемые форматы: изображения, PDF, DOC, DOCX, TXT
                                </p>
                                
                                <!-- Список выбранных файлов -->
                                <div x-show="selectedFiles.length > 0" x-cloak class="mt-4 space-y-2">
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Выбрано файлов: <span x-text="selectedFiles.length"></span>
                                    </p>
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        <template x-for="(file, index) in selectedFiles" :key="index">
                                            <div class="flex items-center gap-3 p-2.5 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                                <i class="fa-solid fa-file text-indigo-600 dark:text-indigo-400 text-sm flex-shrink-0"></i>
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
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_internal" value="1" class="rounded border-slate-300">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">Внутренний комментарий</span>
                                </label>
                                <button type="submit" 
                                    :disabled="submitting"
                                    class="ml-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors">
                                    <i class="fa-solid mr-2" :class="submitting ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                                    <span x-text="submitting ? 'Отправка...' : 'Отправить'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Боковая панель -->
            <div class="space-y-6">
                <!-- Действия -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Действия</h3>
                    
                    <div class="space-y-4">
                        @can('panel.tickets.assign')
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Назначить на</label>
                            <form method="POST" action="{{ route('panel.tickets.assign', $ticket) }}" class="inline-block w-full">
                                @csrf
                                <select name="assigned_to" onchange="this.form.submit()" 
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <option value="">Не назначен</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        @endcan

                        @if(auth()->user()->can('panel.tickets.update') || $ticket->assigned_to === auth()->id())
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Изменить статус</label>
                            <form method="POST" action="{{ route('panel.tickets.status', $ticket) }}" class="inline-block w-full">
                                @csrf
                                <select name="status" onchange="this.form.submit()" 
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <option value="new" {{ $ticket->status === 'new' ? 'selected' : '' }}>Новый</option>
                                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>В работе</option>
                                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Решен</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Закрыт</option>
                                </select>
                            </form>
                        </div>
                        @endif

                        @can('panel.tickets.update')
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('panel.tickets.edit', $ticket) }}"
                                class="block w-full text-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                <i class="fa-solid fa-edit mr-2"></i>Редактировать
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Информация -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Информация</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-slate-500 dark:text-slate-400 mb-1">Бизнес</p>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $ticket->business->name }}</p>
                        </div>
                        @if($ticket->client)
                            <div>
                                <p class="text-slate-500 dark:text-slate-400 mb-1">Клиент</p>
                                <p class="font-medium text-slate-900 dark:text-white">
                                    {{ $ticket->client->first_name }} {{ $ticket->client->last_name }}
                                </p>
                            </div>
                        @endif
                        <div>
                            <p class="text-slate-500 dark:text-slate-400 mb-1">Создан</p>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $ticket->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        @if($ticket->updated_at != $ticket->created_at)
                            <div>
                                <p class="text-slate-500 dark:text-slate-400 mb-1">Обновлен</p>
                                <p class="font-medium text-slate-900 dark:text-white">{{ $ticket->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function fileUploadAdmin() {
            return {
                selectedFiles: [],
                
                handleFiles(event) {
                    const files = Array.from(event.target.files);
                    this.addFiles(files);
                },
                
                addFiles(files) {
                    const maxSize = 10 * 1024 * 1024; // 10 МБ
                    files.forEach(file => {
                        if (file.size > maxSize) {
                            alert(`Файл "${file.name}" слишком большой. Максимальный размер: 10 МБ`);
                            return;
                        }
                        if (!this.selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
                            this.selectedFiles.push(file);
                        }
                    });
                },
                
                removeFile(index) {
                    const fileToRemove = this.selectedFiles[index];
                    this.selectedFiles.splice(index, 1);
                    
                    // Удаляем файл из input
                    const input = document.getElementById('attachments_admin');
                    const dataTransfer = new DataTransfer();
                    
                    // Добавляем все файлы кроме удаленного
                    Array.from(input.files).forEach(file => {
                        if (file.name !== fileToRemove.name || file.size !== fileToRemove.size) {
                            dataTransfer.items.add(file);
                        }
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
@endsection
