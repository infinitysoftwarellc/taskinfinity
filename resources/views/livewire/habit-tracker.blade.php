<div>
    {{-- Formulário para adicionar novo hábito --}}
    <form wire:submit.prevent="addHabit" class="mb-6 p-4 border rounded-lg">
        <h3 class="text-lg font-semibold mb-4">Novo Hábito</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="newHabitName" :value="__('Nome do Hábito')" />
                <x-text-input wire:model="newHabitName" id="newHabitName" class="block mt-1 w-full" type="text" />
            </div>
            <div>
                <x-input-label for="newHabitFrequency" :value="__('Frequência')" />
                <select wire:model="newHabitFrequency" id="newHabitFrequency" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="daily">Diário</option>
                    <option value="weekly">Semanal</option>
                </select>
            </div>
            <div>
                <x-input-label for="newHabitStartDate" :value="__('Data de Início')" />
                <x-text-input wire:model="newHabitStartDate" id="newHabitStartDate" class="block mt-1 w-full" type="date" />
            </div>
            <div>
                <x-input-label for="newHabitGoalDays" :value="__('Dias de Meta')" />
                <x-text-input wire:model="newHabitGoalDays" id="newHabitGoalDays" class="block mt-1 w-full" type="number" />
            </div>
        </div>
        <div class="mt-4">
            <x-primary-button>
                {{ __('Adicionar Hábito') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Tabela de Hábitos --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Hábito
                    </th>
                    @foreach ($dates as $date)
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $date->format('d/m') }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($habits as $habit)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $habit->name }}</div>
                        </td>
                        @foreach ($dates as $date)
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="checkbox"
                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                    wire:click="toggleCompletion({{ $habit->id }}, '{{ $date->toDateString() }}')"
                                    @if ($habit->completions->where('completed_at', $date->toDateString())->isNotEmpty()) checked @endif
                                >
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>