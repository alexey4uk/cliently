@if (session()->hasAny(['success', 'info', 'error', 'warning', 'password_success']))
    <!-- Toast контейнер для уведомлений -->
    <div class="fixed bottom-4 right-4 z-50 space-y-3" id="toast-container">
        @if(session('success'))
            <x-toast-notification type="success" :message="session('success')" />
        @endif

        @if(session('info'))
            <x-toast-notification type="info" :message="session('info')" />
        @endif

        @if(session('error'))
            <x-toast-notification type="error" :message="session('error')" />
        @endif

        @if(session('warning'))
            <x-toast-notification type="warning" :message="session('warning')" />
        @endif

        @if(session('password_success'))
            <x-toast-notification type="success" :message="session('password_success')" />
        @endif
    </div>
@endif
