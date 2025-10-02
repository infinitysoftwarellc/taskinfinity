<?php

namespace App\Livewire\Tasks;

use App\Models\Folder;
use App\Models\Mission;
use App\Models\Tag;
use App\Models\TaskList;
use App\Support\MissionShortcutFilter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Componente da barra lateral da página de tarefas, responsável por listas, pastas e tags.
 */
class Sidebar extends Component
{
    /**
     * Eventos monitorados para atualizar a barra lateral automaticamente.
     */
    protected $listeners = ['tasks-updated' => '$refresh'];

    public string $workspaceTitle = 'Listas';

    public ?int $currentListId = null;

    public ?string $currentShortcut = null;

    public bool $completedView = false;

    public bool $showCreateModal = false;

    public string $newListType = 'list';

    public ?int $editingListId = null;

    public ?int $editingFolderId = null;

    public ?string $openMenuId = null;

    public array $viewTypes = [
        'lista' => 'Lista',
        'kanban' => 'Kanban',
        'calendario' => 'Calendário',
        'cronograma' => 'Cronograma',
    ];

    public array $colorPalette = [
        '#7aa2ff',
        '#ff8a65',
        '#f6c177',
        '#8be9fd',
        '#ff6bcb',
        '#34d399',
        '#f97316',
        '#a78bfa',
    ];

    public string $newListName = '';

    public ?string $newListColor = null;

    public ?string $newListIcon = null;

    public string $newListViewType = '';

    public ?int $newListFolder = null;

    public bool $showTagModal = false;

    public string $newTagName = '';

    public string $newTagColor = '#7aa2ff';

    /**
     * Inicializa a barra lateral com os filtros selecionados.
     */
    public function mount(?int $currentListId = null, ?string $currentShortcut = null, bool $completedView = false): void
    {
        $this->currentListId = $currentListId;
        $this->currentShortcut = $currentShortcut;
        $this->completedView = $completedView;
        $this->ensureFormDefaults();
    }

    /**
     * Abre o modal para criar ou editar listas e pastas.
     */
    public function openCreateModal(string $entity = 'list', ?int $id = null): void
    {
        $type = $entity === 'folder' ? 'folder' : 'list';

        $this->resetValidation();
        $this->openMenuId = null;
        $this->showCreateModal = true;
        $this->newListType = $type;
        $this->editingListId = null;
        $this->editingFolderId = null;

        if ($type === 'folder' && $id) {
            $this->fillFormForFolder($id);
        } elseif ($type === 'list' && $id) {
            $this->fillFormForList($id);
        } else {
            $this->resetCreateForm($type);
        }
    }

    /**
     * Fecha o modal e limpa os dados temporários do formulário.
     */
    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetCreateForm('list');
    }

    /**
     * Controla a abertura de menus contextuais de cada item.
     */
    public function toggleMenu(string $key): void
    {
        $this->openMenuId = $this->openMenuId === $key ? null : $key;
    }

    /**
     * Força o fechamento de qualquer menu aberto na sidebar.
     */
    public function closeMenu(): void
    {
        $this->openMenuId = null;
    }

    /**
     * Roteia o salvamento para lista ou pasta conforme o estado atual.
     */
    public function saveList(): void
    {
        if ($this->editingFolderId) {
            $this->newListType = 'folder';
        } elseif ($this->editingListId) {
            $this->newListType = 'list';
        }

        if ($this->newListType === 'folder') {
            $this->saveFolder();

            return;
        }

        $this->saveTaskList();
    }

    /**
     * Cria uma cópia da lista selecionada mantendo os dados principais.
     */
    public function duplicateList(int $listId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $list = TaskList::query()
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->find($listId);

        if (! $list) {
            return;
        }

        $copy = $list->replicate([
            'position',
            'is_pinned',
            'archived_at',
            'created_at',
            'updated_at',
        ]);

        $copy->name = $this->duplicateListName($list, $list->folder_id);
        $copy->position = $this->nextListPosition($user->id, $list->folder_id);
        $copy->is_pinned = false;
        $copy->archived_at = null;
        $copy->save();

        $this->dispatch('tasks-updated');
        $this->closeMenu();
    }

    /**
     * Duplica uma pasta inteira com suas listas filhas.
     */
    public function duplicateFolder(int $folderId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $folder = Folder::query()
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->with(['lists' => function ($query) {
                $query->whereNull('archived_at');
            }])
            ->find($folderId);

        if (! $folder) {
            return;
        }

        $copy = $folder->replicate([
            'position',
            'is_pinned',
            'archived_at',
            'created_at',
            'updated_at',
        ]);

        $copy->name = $this->duplicateFolderName($folder);
        $copy->position = $this->nextFolderPosition($user->id);
        $copy->is_pinned = false;
        $copy->archived_at = null;
        $copy->save();

        foreach ($folder->lists as $list) {
            $newList = $list->replicate([
                'position',
                'is_pinned',
                'archived_at',
                'created_at',
                'updated_at',
            ]);

            $newList->name = $this->duplicateListName($list, $copy->id);
            $newList->folder_id = $copy->id;
            $newList->position = $this->nextListPosition($user->id, $copy->id);
            $newList->is_pinned = false;
            $newList->archived_at = null;
            $newList->save();
        }

        $this->dispatch('tasks-updated');
        $this->closeMenu();
    }

    /**
     * Alterna a fixação (pin) de uma lista específica.
     */
    public function togglePin(int $listId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $list = TaskList::query()
            ->where('user_id', $user->id)
            ->find($listId);

        if (! $list) {
            return;
        }

        $list->is_pinned = ! $list->is_pinned;
        $list->save();

        $this->dispatch('tasks-updated');
        $this->closeMenu();
    }

    /**
     * Alterna a fixação de uma pasta.
     */
    public function togglePinFolder(int $folderId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $folder = Folder::query()
            ->where('user_id', $user->id)
            ->find($folderId);

        if (! $folder) {
            return;
        }

        $folder->is_pinned = ! $folder->is_pinned;
        $folder->save();

        $this->dispatch('tasks-updated');
        $this->closeMenu();
    }

    /**
     * Arquiva ou desarquiva uma lista sem removê-la definitivamente.
     */
    public function toggleArchive(int $listId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $list = TaskList::query()
            ->where('user_id', $user->id)
            ->find($listId);

        if (! $list) {
            return;
        }

        $list->archived_at = $list->archived_at ? null : Carbon::now();
        $list->save();

        if ($this->currentListId === $listId && $list->archived_at) {
            $this->currentListId = null;
        }

        $this->dispatch('tasks-updated');
        $this->closeMenu();
    }

    /**
     * Arquiva ou restaura uma pasta e suas listas filhas.
     */
    public function toggleArchiveFolder(int $folderId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $folder = Folder::query()
            ->where('user_id', $user->id)
            ->with('lists')
            ->find($folderId);

        if (! $folder) {
            return;
        }

        $folder->archived_at = $folder->archived_at ? null : Carbon::now();
        $folder->save();

        TaskList::query()
            ->where('user_id', $user->id)
            ->where('folder_id', $folder->id)
            ->update(['archived_at' => $folder->archived_at]);

        if ($folder->archived_at && $folder->lists->contains('id', $this->currentListId)) {
            $this->currentListId = null;
        }

        $this->dispatch('tasks-updated');
        $this->closeMenu();
    }

    /**
     * Arquiva definitivamente uma lista do usuário.
     */
    public function deleteList(int $listId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $list = TaskList::query()
            ->where('user_id', $user->id)
            ->find($listId);

        if (! $list) {
            return;
        }

        $list->delete();

        if ($this->currentListId === $listId) {
            $this->currentListId = null;
        }

        $this->dispatch('tasks-updated');
        $this->closeMenu();
    }

    /**
     * Exclui uma pasta juntamente com suas listas relacionadas.
     */
    public function deleteFolder(int $folderId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $folder = Folder::query()
            ->where('user_id', $user->id)
            ->find($folderId);

        if (! $folder) {
            return;
        }

        $folder->delete();

        $this->dispatch('tasks-updated');
        $this->closeMenu();
    }

    /**
     * Exibe o modal para criação de novas tags.
     */
    public function openTagModal(): void
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->resetTagForm();

        $this->showTagModal = true;
    }

    /**
     * Fecha o modal de tags e limpa o formulário.
     */
    public function closeTagModal(): void
    {
        $this->showTagModal = false;
        $this->resetTagForm();
    }

    /**
     * Salva uma nova tag associada às tarefas do usuário.
     */
    public function createTag(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $validated = $this->validate(
            [
                'newTagName' => 'required|string|max:100',
                'newTagColor' => 'nullable|string|max:20',
            ],
            [],
            [
                'newTagName' => 'nome da tag',
                'newTagColor' => 'cor',
            ]
        );

        Tag::updateOrCreate(
            [
                'user_id' => $user->id,
                'name' => trim($validated['newTagName']),
            ],
            [
                'color' => $validated['newTagColor'] ?? null,
            ]
        );

        $this->resetTagForm();
        $this->showTagModal = false;
        $this->dispatch('tasks-updated');
    }

    /**
     * Renderiza a view com listas, pastas e tags formatadas para a Tasks page.
     */
    public function render()
    {
        $this->ensureFormDefaults();

        $user = Auth::user();

        if (! $user) {
            return view('livewire.tasks.sidebar', [
                'shortcuts' => [],
                'workspaceExpanded' => true,
                'workspaceBadge' => 0,
                'workspaceTitle' => $this->workspaceTitle,
                'folders' => collect(),
                'standaloneLists' => collect(),
                'folderOptions' => collect(),
                'tags' => collect(),
                'completedLabel' => 'Completed',
                'completedCount' => 0,
                'completedActive' => $this->completedView,
                'completedHref' => route('tasks.completed'),
                'showTagModal' => $this->showTagModal,
                'viewTypes' => $this->viewTypes,
                'colorPalette' => $this->colorPalette,
                'openMenuId' => $this->openMenuId,
                'currentListId' => $this->currentListId,
                'editingFolderId' => $this->editingFolderId,
                'editingListId' => $this->editingListId,
            ]);
        }

        $missionQuery = Mission::query()->where('user_id', $user->id);

        $totalTasks = (clone $missionQuery)->count();
        $completedTasks = (clone $missionQuery)->where('status', 'done')->count();

        $timezone = $user->timezone ?? config('app.timezone');

        $todayTasks = MissionShortcutFilter::apply(
            (clone $missionQuery),
            MissionShortcutFilter::TODAY,
            $timezone
        )->count();

        $tomorrowTasks = MissionShortcutFilter::apply(
            (clone $missionQuery),
            MissionShortcutFilter::TOMORROW,
            $timezone
        )->count();

        $nextSevenDaysTasks = MissionShortcutFilter::apply(
            (clone $missionQuery),
            MissionShortcutFilter::NEXT_SEVEN_DAYS,
            $timezone
        )->count();

        $folders = Folder::query()
            ->with(['lists' => function ($query) {
                $query
                    ->withCount('missions')
                    ->whereNull('archived_at')
                    ->orderByDesc('is_pinned')
                    ->orderBy('position')
                    ->orderBy('name');
            }])
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->orderByDesc('is_pinned')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $standaloneLists = TaskList::query()
            ->withCount('missions')
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->whereNull('folder_id')
            ->orderByDesc('is_pinned')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $workspaceBadge = $standaloneLists->sum('missions_count')
            + $folders->sum(fn ($folder) => $folder->lists->sum('missions_count'));

        $folderOptions = Folder::query()
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->orderBy('name')
            ->get();

        $tags = Tag::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        $shortcuts = [
            [
                'icon' => 'infinity',
                'label' => 'All',
                'count' => $totalTasks,
                'href' => route('tasks.index'),
                'active' => $this->currentListId === null && $this->currentShortcut === null,
            ],
            [
                'icon' => 'sun',
                'label' => 'Today',
                'count' => $todayTasks,
                'href' => route('tasks.index', ['shortcut' => MissionShortcutFilter::TODAY]),
                'active' => $this->currentShortcut === MissionShortcutFilter::TODAY,
            ],
            [
                'icon' => 'sunrise',
                'label' => 'Tomorrow',
                'count' => $tomorrowTasks,
                'href' => route('tasks.index', ['shortcut' => MissionShortcutFilter::TOMORROW]),
                'active' => $this->currentShortcut === MissionShortcutFilter::TOMORROW,
            ],
            [
                'icon' => 'calendar-days',
                'label' => 'Next 7 Days',
                'count' => $nextSevenDaysTasks,
                'href' => route('tasks.index', ['shortcut' => MissionShortcutFilter::NEXT_SEVEN_DAYS]),
                'active' => $this->currentShortcut === MissionShortcutFilter::NEXT_SEVEN_DAYS,
            ],
        ];

        return view('livewire.tasks.sidebar', [
            'shortcuts' => $shortcuts,
            'workspaceExpanded' => true,
            'workspaceBadge' => $workspaceBadge,
            'workspaceTitle' => $this->workspaceTitle,
            'folders' => $folders,
            'standaloneLists' => $standaloneLists,
            'folderOptions' => $folderOptions,
            'tags' => $tags,
            'completedLabel' => 'Completed',
            'completedCount' => $completedTasks,
            'completedActive' => $this->completedView,
            'completedHref' => route('tasks.completed'),
            'showTagModal' => $this->showTagModal,
            'viewTypes' => $this->viewTypes,
            'colorPalette' => $this->colorPalette,
            'openMenuId' => $this->openMenuId,
            'currentListId' => $this->currentListId,
            'editingFolderId' => $this->editingFolderId,
            'editingListId' => $this->editingListId,
        ]);
    }

    /**
     * Salva as informações da lista preenchidas no modal.
     */
    protected function saveTaskList(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        if ($this->newListFolder === '' || $this->newListFolder === 0) {
            $this->newListFolder = null;
        }

        $this->newListName = trim($this->newListName);
        $defaultView = array_key_first($this->viewTypes) ?? 'lista';

        $validated = $this->validate(
            [
                'newListName' => 'required|string|max:255',
                'newListColor' => 'nullable|string|max:20',
                'newListIcon' => 'nullable|string|max:100',
                'newListViewType' => 'required|string|max:40',
                'newListFolder' => [
                    'nullable',
                    'integer',
                    Rule::exists('folders', 'id')->where(
                        fn ($query) => $query
                            ->where('user_id', $user->id)
                            ->whereNull('archived_at')
                    ),
                ],
            ],
            [],
            [
                'newListName' => 'nome',
                'newListColor' => 'cor',
                'newListIcon' => 'ícone',
                'newListViewType' => 'visualização',
                'newListFolder' => 'pasta',
            ]
        );

        $folderId = $validated['newListFolder'] ?? null;

        if ($this->editingListId) {
            $list = TaskList::query()
                ->where('user_id', $user->id)
                ->find($this->editingListId);

            if (! $list) {
                $this->closeCreateModal();

                return;
            }

            $originalFolder = $list->folder_id;

            $list->fill([
                'name' => trim($validated['newListName']),
                'color' => $validated['newListColor'] ?: null,
                'icon' => $validated['newListIcon'] ?: null,
                'view_type' => $validated['newListViewType'] ?: $defaultView,
                'folder_id' => $folderId,
            ]);

            if ($originalFolder !== $list->folder_id) {
                $list->position = $this->nextListPosition($user->id, $list->folder_id);
            }

            $list->save();
        } else {
            $position = $this->nextListPosition($user->id, $folderId);

            TaskList::create([
                'user_id' => $user->id,
                'name' => trim($validated['newListName']),
                'view_type' => $validated['newListViewType'] ?: $defaultView,
                'color' => $validated['newListColor'] ?: null,
                'icon' => $validated['newListIcon'] ?: null,
                'folder_id' => $folderId,
                'position' => $position,
                'is_pinned' => false,
            ]);
        }

        $this->dispatch('tasks-updated');
        $this->closeCreateModal();
    }

    /**
     * Persiste os dados da pasta editada ou recém-criada.
     */
    protected function saveFolder(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->newListName = trim($this->newListName);

        $validated = $this->validate(
            [
                'newListName' => 'required|string|max:255',
                'newListColor' => 'nullable|string|max:20',
            ],
            [],
            [
                'newListName' => 'nome',
                'newListColor' => 'cor',
            ]
        );

        if ($this->editingFolderId) {
            $folder = Folder::query()
                ->where('user_id', $user->id)
                ->find($this->editingFolderId);

            if (! $folder) {
                $this->closeCreateModal();

                return;
            }

            $folder->fill([
                'name' => trim($validated['newListName']),
                'color' => $validated['newListColor'] ?: null,
            ]);
            $folder->save();
        } else {
            $position = $this->nextFolderPosition($user->id);

            Folder::create([
                'user_id' => $user->id,
                'name' => trim($validated['newListName']),
                'color' => $validated['newListColor'] ?: null,
                'position' => $position,
                'is_pinned' => false,
            ]);
        }

        $this->dispatch('tasks-updated');
        $this->closeCreateModal();
    }

    protected function nextListPosition(int $userId, ?int $folderId = null): int
    {
        return (int) TaskList::query()
            ->where('user_id', $userId)
            ->whereNull('archived_at')
            ->when(
                $folderId,
                fn ($query) => $query->where('folder_id', $folderId),
                fn ($query) => $query->whereNull('folder_id')
            )
            ->max('position') + 1;
    }

    protected function nextFolderPosition(int $userId): int
    {
        return (int) Folder::query()
            ->where('user_id', $userId)
            ->whereNull('archived_at')
            ->max('position') + 1;
    }

    protected function duplicateListName(TaskList $list, ?int $folderId = null): string
    {
        $base = $list->name . ' (Cópia)';
        $userId = $list->user_id;
        $folderContext = $folderId ?? $list->folder_id;

        $query = TaskList::query()
            ->where('user_id', $userId)
            ->where('name', $base)
            ->when(
                $folderContext,
                fn ($q) => $q->where('folder_id', $folderContext),
                fn ($q) => $q->whereNull('folder_id')
            );

        if (! $query->exists()) {
            return $base;
        }

        $suffix = 2;

        do {
            $candidate = $base . ' ' . $suffix;
            $suffix++;
        } while (
            TaskList::query()
                ->where('user_id', $userId)
                ->where('name', $candidate)
                ->when(
                    $folderContext,
                    fn ($q) => $q->where('folder_id', $folderContext),
                    fn ($q) => $q->whereNull('folder_id')
                )
                ->exists()
        );

        return $candidate;
    }

    protected function duplicateFolderName(Folder $folder): string
    {
        $base = $folder->name . ' (Cópia)';
        $userId = $folder->user_id;

        if (! Folder::query()->where('user_id', $userId)->where('name', $base)->exists()) {
            return $base;
        }

        $suffix = 2;

        do {
            $candidate = $base . ' ' . $suffix;
            $suffix++;
        } while (Folder::query()->where('user_id', $userId)->where('name', $candidate)->exists());

        return $candidate;
    }

    protected function ensureFormDefaults(): void
    {
        $defaultColor = $this->colorPalette[0] ?? '#7aa2ff';
        $defaultView = array_key_first($this->viewTypes) ?? 'lista';

        if (! $this->newListColor) {
            $this->newListColor = $defaultColor;
        }

        if (! $this->newListViewType) {
            $this->newListViewType = $defaultView;
        }

        if (! $this->newTagColor) {
            $this->newTagColor = $defaultColor;
        }
    }

    protected function resetTagForm(): void
    {
        $this->newTagName = '';
        $this->newTagColor = $this->colorPalette[0] ?? '#7aa2ff';
    }

    protected function resetCreateForm(string $type = 'list'): void
    {
        $this->newListType = $type;
        $this->editingListId = null;
        $this->editingFolderId = null;
        $this->newListName = '';
        $this->newListIcon = null;
        $this->newListFolder = null;
        $this->newListColor = $this->colorPalette[0] ?? '#7aa2ff';
        $this->newListViewType = array_key_first($this->viewTypes) ?? 'lista';
    }

    protected function fillFormForList(int $listId): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->resetCreateForm('list');

            return;
        }

        $list = TaskList::query()
            ->where('user_id', $user->id)
            ->find($listId);

        if (! $list) {
            $this->resetCreateForm('list');

            return;
        }

        $this->editingListId = $list->id;
        $this->newListType = 'list';
        $this->newListName = $list->name;
        $this->newListIcon = $list->icon;
        $this->newListColor = $list->color ?? ($this->colorPalette[0] ?? '#7aa2ff');
        $this->newListViewType = $list->view_type ?? (array_key_first($this->viewTypes) ?? 'lista');
        $this->newListFolder = $list->folder_id;
    }

    protected function fillFormForFolder(int $folderId): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->resetCreateForm('folder');

            return;
        }

        $folder = Folder::query()
            ->where('user_id', $user->id)
            ->find($folderId);

        if (! $folder) {
            $this->resetCreateForm('folder');

            return;
        }

        $this->editingFolderId = $folder->id;
        $this->newListType = 'folder';
        $this->newListName = $folder->name;
        $this->newListColor = $folder->color ?? ($this->colorPalette[0] ?? '#7aa2ff');
        $this->newListViewType = array_key_first($this->viewTypes) ?? 'lista';
        $this->newListFolder = null;
    }
}
