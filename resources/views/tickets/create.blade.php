@extends('layouts.user')

@section('title', 'Создать тикет - Cliently')
@section('page-title', 'Создать тикет')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Тикеты', 'url' => route('tickets.index')],
        ['title' => 'Создать тикет', 'url' => null]
    ]" />
@endpush

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Создать новый тикет</h2>
                <p class="text-slate-600 dark:text-slate-400">Опишите вашу проблему или вопрос, и мы поможем вам</p>
            </div>

            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Тема тикета <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        placeholder="Кратко опишите проблему или вопрос"
                        class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder:text-slate-400">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Описание <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="8" required
                        placeholder="Подробно опишите вашу проблему или вопрос. Чем больше деталей вы предоставите, тем быстрее мы сможем помочь."
                        class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none placeholder:text-slate-400">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-tag mr-2 text-slate-400"></i>
                        Категория
                    </label>
                    <select name="category_id" 
                        class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">Выберите категорию (необязательно)</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
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
                                <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Выберите файлы</span>
                                    <input id="file-upload" name="attachments[]" type="file" multiple accept="image/*,.pdf,.doc,.docx,.txt" class="sr-only" @change="handleFiles($event)">
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

                <div class="flex items-center gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-paper-plane"></i>
                        Создать тикет
                    </button>
                    <a href="{{ route('tickets.index') }}"
                        class="px-6 py-3 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg font-medium transition-colors">
                        Отмена
                    </a>
                </div>
            </form>
        </div>

        <!-- Подсказки -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-lightbulb text-blue-600 dark:text-blue-400 mt-0.5"></i>
                <div class="text-sm text-blue-800 dark:text-blue-300">
                    <p class="font-medium mb-1">Совет:</p>
                    <p>Для более быстрого решения проблемы укажите как можно больше деталей: что вы делали, когда возникла проблема, какие сообщения об ошибках вы видели (если были).</p>
                </div>
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
                    const input = document.getElementById('file-upload');
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
                    this.selectedFiles.splice(index, 1);
                    this.updateInput();
                },
                
                updateInput() {
                    const input = document.getElementById('file-upload');
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
@endsection
