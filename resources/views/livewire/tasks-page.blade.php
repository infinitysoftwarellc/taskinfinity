<div>
    {{-- Header with project name --}}
    <h1 class="text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
    <p class="text-gray-600">{{ $project->description }}</p>

    <div class="mt-8">
        {{-- Container for lists --}}
        <div class="flex space-x-4 overflow-x-auto pb-4">

            {{-- Loop through each list in the project --}}
            @foreach ($project->taskLists as $list)
                <div class="bg-gray-100 rounded-lg p-4 w-72 flex-shrink-0">
                    <h3 class="font-bold text-lg text-gray-800">{{ $list->name }}</h3>
                    
                    {{-- Task items will go here in the future --}}
                    <div class="mt-4 space-y-2">
                        @foreach($list->tasks as $task)
                            <div class="bg-white p-2 rounded shadow">
                                {{ $task->title }}
                            </div>
                        @endforeach
                        {{-- Placeholder for new task form --}}
                        <div class="text-gray-500 cursor-pointer hover:text-gray-700">
                            + Add a card
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Form to add a new list --}}
            <div class="w-72 flex-shrink-0">
                <div class="bg-gray-200 rounded-lg p-2">
                    <form wire:submit.prevent="addList">
                        <input 
                            type="text" 
                            wire:model.defer="newListName" 
                            placeholder="+ Add another list" 
                            class="w-full bg-transparent border-none rounded-md focus:ring-2 focus:ring-blue-500 placeholder-gray-600"
                        >
                        <div class="mt-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Add List
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>