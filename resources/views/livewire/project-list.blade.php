// resources/views/livewire/project-list.blade.php
<aside class="w-64 bg-gray-800 border-r border-gray-700 p-4 overflow-y-auto no-scrollbar">
    <nav class="space-y-2">
        <a href="{{ route('tasks.filter', 'inbox') }}" class="flex items-center p-2 rounded-md hover:bg-gray-700">
            <i class="fas fa-inbox mr-3"></i> Inbox
        </a>
        <a href="{{ route('tasks.filter', 'today') }}" class="flex items-center p-2 rounded-md hover:bg-gray-700">
            <i class="fas fa-calendar-day mr-3"></i> Today
        </a>
        <a href="{{ route('tasks.filter', 'upcoming') }}" class="flex items-center p-2 rounded-md hover:bg-gray-700">
            <i class="fas fa-calendar-alt mr-3"></i> Upcoming
        </a>
    </nav>

    <div class="mt-8">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Projects</h2>
        <div class="mt-2 space-y-1">
            @foreach ($projects as $project)
                <a href="{{ route('tasks.project', $project) }}" class="flex items-center p-2 rounded-md hover:bg-gray-700">
                    <span class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $project->color ?? '#fff' }}"></span>
                    <span>{{ $project->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</aside>