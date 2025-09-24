{{-- resources/views/livewire/create-project-form.blade.php --}}

<form wire:submit.prevent="saveProject" class="mt-4 border-t border-gray-700 pt-4">
    <h3 class="text-sm font-semibold text-gray-300">New Project</h3>
    <div class="mt-2">
        <input type="text" 
               wire:model="name" 
               placeholder="Project Name" 
               class="w-full bg-gray-900 border-gray-700 rounded-md text-sm text-white">
        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
    </div>
    <div class="mt-2">
        <input type="color" 
               wire:model="color" 
               class="w-full bg-gray-900 border-gray-700 rounded-md h-8 p-0 cursor-pointer">
    </div>
    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-2 rounded-md mt-3 text-sm">
        Create Project
    </button>
</form>