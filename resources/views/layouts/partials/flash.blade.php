@if (session('status'))
    <div class="fixed top-4 right-4 z-50 max-w-xs rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white shadow-lg" role="status">
        {{ __(session('status')) }}
    </div>
@endif
