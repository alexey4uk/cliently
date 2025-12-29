{{-- Компонент скриптов валидации формы для страниц авторизации --}}
@props([
    'formId', // ID формы
    'submitText' => 'Отправка...', // Текст при отправке
])

<script>
    // Улучшение UX формы
    const form = document.getElementById('{{ $formId }}');
    if (form) {
        const submitBtn = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg><span> {{ $submitText }}</span>';
            submitBtn.disabled = true;
        });

        // Валидация в реальном времени
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                } else {
                    this.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                }
            });
        });
    }
</script>

