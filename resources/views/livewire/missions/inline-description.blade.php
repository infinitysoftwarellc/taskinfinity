<div
    x-data="inlineDesc({
        missionId: {{ $missionId }},
        initialValue: @js($description ?? ''),
        placeholder: 'Escreva algo ou digite / para comandos',
        debounceMs: 600,
    }, $wire)"
    x-init="init()"
    x-bind:class="{'is-editing': isEditing}"
    class="inline-desc-container"
>
    <div class="inline-desc-display" x-show="!isEditing" x-transition.opacity.duration.150ms>
        <button
            type="button"
            class="inline-desc-button"
            @click="enterEdit()"
        >
            @if (blank($description))
                <span class="inline-desc-placeholder" aria-label="Escreva algo ou digite barra para abrir comandos">Escreva algo ou digite / para comandos</span>
            @else
                <span class="inline-desc-text" x-html="displayValue"></span>
            @endif
        </button>
    </div>

    <div class="inline-desc-editor" x-show="isEditing" x-cloak>
        <div class="editor-shell">
            <div
                id="mission-inline-description-{{ $missionId }}"
                class="editor-area"
                contenteditable="true"
                wire:ignore
                x-ref="editor"
                role="textbox"
                aria-multiline="true"
                :aria-label="placeholder"
                x-on:input="onInput($event)"
                x-on:keydown="handleKeydown($event)"
                x-on:blur="onBlur($event)"
                x-on:paste.prevent="onPaste($event)"
                x-on:click="rememberSelection()"
                x-on:mouseup="rememberSelection()"
            ></div>

            <div class="editor-status" x-show="statusText" x-transition.opacity.duration.200ms>
                <span x-text="statusText"></span>
            </div>

            <div
                class="slash-menu"
                x-show="showMenu && filteredCommands.length"
                x-transition.opacity.duration.120ms
                role="listbox"
                :aria-activedescendant="activeCommandId"
            >
                <template x-for="(command, index) in filteredCommands" :key="command.id">
                    <div
                        class="slash-menu-item"
                        :class="{'is-active': index === highlightedIndex}"
                        role="option"
                        :id="command.id"
                        :aria-selected="index === highlightedIndex"
                        @mousedown.prevent="applyCommand(command)"
                        @mouseenter="highlightedIndex = index"
                    >
                        <span class="command-label" x-text="command.label"></span>
                        <span class="command-hint" x-text="command.hint"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@pushOnce('styles', 'inline-desc-styles')
    <style>
        :root {
            --inline-desc-bg: var(--bg, #f7f9fc);
            --inline-desc-fg: var(--fg, #22272f);
            --inline-desc-muted: var(--muted, rgba(100, 116, 139, 0.8));
            --inline-desc-border: var(--border, rgba(148, 163, 184, 0.4));
            --inline-desc-ring: rgba(59, 130, 246, 0.35);
            --inline-desc-shadow: rgba(15, 23, 42, 0.12);
            --inline-desc-menu-bg: color-mix(in srgb, var(--bg, #0f172a) 6%, transparent 94%);
        }

        .inline-desc-container {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
            color: var(--inline-desc-fg);
        }

        .inline-desc-button {
            width: 100%;
            text-align: left;
            background: transparent;
            border: none;
            padding: 0.75rem 0.85rem;
            min-height: 3.25rem;
            color: inherit;
            border-radius: 0.9rem;
            transition: background 120ms ease, border-color 120ms ease;
            cursor: text;
        }

        .inline-desc-button:hover,
        .inline-desc-button:focus-visible {
            background: color-mix(in srgb, var(--inline-desc-border) 30%, transparent 70%);
            outline: 2px solid transparent;
            box-shadow: 0 0 0 2px var(--inline-desc-ring);
        }

        .inline-desc-placeholder {
            color: var(--inline-desc-muted);
            font-size: 0.95rem;
            font-weight: 400;
        }

        .inline-desc-text {
            display: block;
            white-space: pre-line;
            font-size: 0.98rem;
        }

        .inline-desc-editor {
            position: relative;
        }

        .editor-shell {
            position: relative;
            background: var(--inline-desc-bg);
            border: 1px solid var(--inline-desc-border);
            border-radius: 1rem;
            padding: 0.9rem 1rem;
            min-height: 3.25rem;
            box-shadow: 0 10px 30px -18px var(--inline-desc-shadow);
            backdrop-filter: blur(6px);
        }

        .inline-desc-container.is-editing .editor-shell {
            border-color: var(--inline-desc-ring);
            box-shadow: 0 0 0 1px var(--inline-desc-ring);
        }

        .editor-area {
            outline: none;
            min-height: 2.25rem;
            line-height: 1.5;
            font-size: 0.98rem;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .editor-area:focus-visible {
            outline: none;
        }

        .editor-status {
            position: absolute;
            right: 1rem;
            bottom: 0.5rem;
            font-size: 0.75rem;
            color: var(--inline-desc-muted);
            pointer-events: none;
            opacity: 0.85;
        }

        .slash-menu {
            position: absolute;
            left: 1rem;
            bottom: -0.35rem;
            transform: translateY(100%);
            background: var(--inline-desc-menu-bg);
            border: 1px solid var(--inline-desc-border);
            border-radius: 0.75rem;
            padding: 0.35rem;
            box-shadow: 0 14px 30px -12px var(--inline-desc-shadow);
            backdrop-filter: blur(10px);
            min-width: 12rem;
            max-height: 15rem;
            overflow-y: auto;
            z-index: 40;
        }

        .slash-menu-item {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            padding: 0.55rem 0.65rem;
            border-radius: 0.65rem;
            cursor: pointer;
            transition: background 100ms ease;
        }

        .slash-menu-item.is-active,
        .slash-menu-item:hover {
            background: color-mix(in srgb, var(--inline-desc-ring) 40%, transparent 60%);
        }

        .command-label {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .command-hint {
            font-size: 0.75rem;
            color: var(--inline-desc-muted);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endPushOnce

@pushOnce('scripts', 'inline-desc-scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('inlineDesc', (config, $wire) => ({
                missionId: config.missionId,
                placeholder: config.placeholder,
                debounceMs: config.debounceMs ?? 600,
                value: config.initialValue ?? '',
                displayValue: config.initialValue ?? '',
                isEditing: false,
                isSaving: false,
                statusText: '',
                statusHideTimer: null,
                debounceTimer: null,
                retryCount: 0,
                lastSavedValue: config.initialValue ?? '',
                showMenu: false,
                menuQuery: '',
                highlightedIndex: 0,
                commandContext: null,
                filteredCommands: [],
                commands: [
                    {
                        id: `cmd-today-${config.missionId}`,
                        key: 'today',
                        label: '/today',
                        hint: 'Insere a data atual',
                        action(component) {
                            const now = new Date();
                            component.insertCommandText(now.toLocaleDateString('pt-BR'));
                        },
                    },
                    {
                        id: `cmd-todo-${config.missionId}`,
                        key: 'todo',
                        label: '/todo',
                        hint: 'Adicionar checkbox (- [ ])',
                        action(component) {
                            component.insertCommandText('- [ ] ');
                        },
                    },
                    {
                        id: `cmd-bold-${config.missionId}`,
                        key: 'bold',
                        label: '/bold',
                        hint: 'Destacar em negrito (** **)',
                        action(component) {
                            component.applyBoldCommand();
                        },
                    },
                    {
                        id: `cmd-tag-${config.missionId}`,
                        key: 'tag',
                        label: '/tag',
                        hint: 'Inserir tag (#)',
                        action(component) {
                            component.insertCommandText('#tag');
                        },
                    },
                    {
                        id: `cmd-time-${config.missionId}`,
                        key: 'time',
                        label: '/time',
                        hint: 'Hora atual',
                        action(component) {
                            const now = new Date();
                            component.insertCommandText(now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }));
                        },
                    },
                ],
                get activeCommandId() {
                    return this.filteredCommands[this.highlightedIndex]?.id ?? null;
                },
                init() {
                    this.displayValue = this.escapeAndFormat(this.value);

                    Livewire.on('description-saving', ({ missionId }) => {
                        if (missionId !== this.missionId) return;
                        this.onSaving();
                    });

                    Livewire.on('description-saved', ({ missionId, skipped }) => {
                        if (missionId !== this.missionId) return;
                        this.onSaved(skipped);
                    });

                    Livewire.on('description-error', ({ missionId }) => {
                        if (missionId !== this.missionId) return;
                        this.onError();
                    });
                },
                enterEdit() {
                    this.isEditing = true;
                    this.$nextTick(() => {
                        this.$refs.editor.focus({ preventScroll: false });
                            this.setCaretPosition(this.value.length);
                        this.rememberSelection();
                    });
                },
                exitEdit() {
                    this.isEditing = false;
                    this.closeMenu();
                    this.displayValue = this.escapeAndFormat(this.value);
                },
                rememberSelection() {
                    const selection = window.getSelection();
                    if (!selection || !selection.rangeCount) {
                        return;
                    }

                    const indices = this.getSelectionIndices();
                    this.commandContext = {
                        ...this.commandContext,
                        selectionStart: indices.start,
                        selectionEnd: indices.end,
                        valueBefore: this.value,
                    };
                },
                onInput() {
                    const plain = this.getPlainText();
                    this.value = plain;
                    this.updateMenuState();
                    this.debouncedSave();
                },
                onPaste(event) {
                    const text = event.clipboardData?.getData('text/plain') ?? '';
                    if (!text) return;
                    this.insertPlainText(text);
                },
                onBlur() {
                    this.closeMenu();
                    this.saveNow(false, true);
                    this.exitEdit();
                },
                onSaving() {
                    this.isSaving = true;
                    this.statusText = 'Salvando…';
                },
                onSaved(skipped) {
                    this.isSaving = false;
                    this.retryCount = 0;
                    if (!skipped) {
                        this.lastSavedValue = this.value;
                    }
                    const label = skipped ? 'Sem alterações' : 'Salvo';
                    this.statusText = label;
                    this.scheduleStatusHide();
                },
                onError() {
                    this.isSaving = false;
                    this.retryCount += 1;
                    this.statusText = 'Falha ao salvar. Tentando novamente…';
                    if (this.retryCount <= 3) {
                        const delay = Math.min(4000, 800 * this.retryCount);
                        setTimeout(() => {
                            this.saveNow();
                        }, delay);
                    } else {
                        console.error('Falha ao salvar descrição da missão', { missionId: this.missionId });
                    }
                },
                scheduleStatusHide() {
                    if (this.statusHideTimer) {
                        clearTimeout(this.statusHideTimer);
                    }
                    this.statusHideTimer = setTimeout(() => {
                        this.statusText = '';
                    }, 1500);
                },
                debouncedSave() {
                    if (this.debounceTimer) {
                        clearTimeout(this.debounceTimer);
                    }
                    this.debounceTimer = setTimeout(() => {
                        this.saveNow();
                    }, this.debounceMs);
                },
                saveNow(force = false, fromBlur = false) {
                    if (this.value === this.lastSavedValue && !force) {
                        return;
                    }

                    this.onSaving();

                    $wire.call('saveDescription', this.value)
                        .catch(() => {
                            this.onError();
                        });

                    if (fromBlur) {
                        this.displayValue = this.escapeAndFormat(this.value);
                    }
                },
                handleKeydown(event) {
                    if (event.key === 'Escape') {
                        if (this.value === this.lastSavedValue) {
                            event.preventDefault();
                            this.exitEdit();
                        }
                        this.closeMenu();
                        return;
                    }

                    if ((event.metaKey || event.ctrlKey) && event.key === 'Enter') {
                        event.preventDefault();
                        this.saveNow(true, true);
                        this.exitEdit();
                        return;
                    }

                    if ((event.metaKey || event.ctrlKey) && (event.key === 'b' || event.key === 'B')) {
                        event.preventDefault();
                        this.applyBoldShortcut();
                        return;
                    }

                    if (event.key === 'Enter' && this.showMenu) {
                        event.preventDefault();
                        const command = this.filteredCommands[this.highlightedIndex];
                        if (command) {
                            this.applyCommand(command);
                        }
                        return;
                    }

                    if (this.showMenu && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
                        event.preventDefault();
                        const max = this.filteredCommands.length - 1;
                        if (max < 0) return;
                        if (event.key === 'ArrowDown') {
                            this.highlightedIndex = (this.highlightedIndex + 1) > max ? 0 : this.highlightedIndex + 1;
                        } else {
                            this.highlightedIndex = (this.highlightedIndex - 1) < 0 ? max : this.highlightedIndex - 1;
                        }
                        return;
                    }

                    if (event.key === '/') {
                        this.commandContext = {
                            valueBefore: this.value,
                            selectionStart: this.getSelectionIndices().start,
                            selectionEnd: this.getSelectionIndices().end,
                        };
                    }

                    if (this.showMenu && event.key === ' ') {
                        this.closeMenu();
                    }
                },
                openMenu(query) {
                    this.showMenu = true;
                    this.menuQuery = query;
                    this.filterCommands();
                    this.highlightedIndex = 0;
                },
                closeMenu() {
                    this.showMenu = false;
                    this.menuQuery = '';
                    this.filteredCommands = [];
                    this.highlightedIndex = 0;
                    this.commandContext = null;
                },
                updateMenuState() {
                    const context = this.getCommandQuery();
                    if (!context) {
                        this.closeMenu();
                        return;
                    }

                    if (!this.commandContext) {
                        this.commandContext = {
                            valueBefore: this.value,
                            selectionStart: context.start,
                            selectionEnd: context.start,
                        };
                    }

                    this.menuQuery = context.query;
                    this.commandContext.queryToken = context.token;
                    this.commandContext.start = context.start;
                    this.commandContext.end = context.end;
                    this.openMenu(context.query);
                },
                filterCommands() {
                    const query = this.menuQuery.toLowerCase();
                    this.filteredCommands = this.commands.filter((command) =>
                        command.key.startsWith(query)
                    );
                },
                applyCommand(command) {
                    if (!this.commandContext) {
                        return;
                    }
                    command.action(this);
                    this.closeMenu();
                    this.debouncedSave();
                },
                insertCommandText(text) {
                    if (!this.commandContext) {
                        return;
                    }
                    const startIndex = this.commandContext.selectionStart ?? this.commandContext.start ?? 0;
                    const endIndex = this.commandContext.selectionEnd ?? this.commandContext.end ?? startIndex;
                    const base = this.commandContext.valueBefore ?? this.value;
                    const before = base.slice(0, startIndex);
                    const after = base.slice(endIndex);
                    this.value = `${before}${text}${after}`;
                    const caret = before.length + text.length;
                    this.setEditorContent(this.value);
                    this.setCaretPosition(caret);
                },
                applyBoldCommand() {
                    const context = this.commandContext;
                    const valueBefore = context?.valueBefore ?? this.value;
                    const selectionStart = context?.selectionStart ?? this.getSelectionIndices().start;
                    const selectionEnd = context?.selectionEnd ?? this.getSelectionIndices().end;
                    const hasSelection = selectionEnd > selectionStart;
                    const selectedText = hasSelection ? valueBefore.slice(selectionStart, selectionEnd) : '';
                    let insertion;
                    let caretOffset;

                    if (hasSelection) {
                        insertion = `**${selectedText}**`;
                        caretOffset = selectionStart + insertion.length;
                    } else {
                        insertion = '**bold**';
                        caretOffset = selectionStart + 2;
                    }

                    const before = valueBefore.slice(0, selectionStart);
                    const after = valueBefore.slice(hasSelection ? selectionEnd : selectionStart);
                    this.value = `${before}${insertion}${after}`;
                    this.setEditorContent(this.value);
                    this.setCaretPosition(caretOffset);
                },
                applyBoldShortcut() {
                    const indices = this.getSelectionIndices();
                    if (indices.end > indices.start) {
                        this.commandContext = {
                            valueBefore: this.value,
                            selectionStart: indices.start,
                            selectionEnd: indices.end,
                        };
                        this.applyBoldCommand();
                        this.debouncedSave();
                    }
                },
                getPlainText() {
                    const html = this.$refs.editor.innerHTML;
                    return this.htmlToPlainText(html);
                },
                setEditorContent(value) {
                    this.$refs.editor.innerHTML = this.escapeHtml(value).replace(/\n/g, '<br>');
                },
                insertPlainText(text) {
                    const selection = window.getSelection();
                    if (!selection || !selection.rangeCount) {
                        return;
                    }

                    const range = selection.getRangeAt(0);
                    range.deleteContents();

                    const fragment = document.createDocumentFragment();
                    const normalized = text.replace(/\r\n?/g, '\n');
                    const lines = normalized.split('\n');
                    let lastNode = null;

                    lines.forEach((line, index) => {
                        const textNode = document.createTextNode(line);
                        fragment.appendChild(textNode);
                        lastNode = textNode;
                        if (index < lines.length - 1) {
                            const br = document.createElement('br');
                            fragment.appendChild(br);
                            lastNode = br;
                        }
                    });

                    range.insertNode(fragment);
                    if (lastNode) {
                        range.setStartAfter(lastNode);
                        range.collapse(true);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    }

                    this.value = this.getPlainText();
                },
                getCommandQuery() {
                    const selection = window.getSelection();
                    if (!selection || !selection.rangeCount) {
                        return null;
                    }
                    const range = selection.getRangeAt(0);
                    const preRange = range.cloneRange();
                    preRange.selectNodeContents(this.$refs.editor);
                    preRange.setEnd(range.endContainer, range.endOffset);
                    const textBefore = this.htmlToPlainText(preRange.toString());
                    const match = textBefore.match(/\/(\w*)$/);
                    if (!match) {
                        return null;
                    }

                    const query = match[1] ?? '';
                    const token = match[0];
                    const start = textBefore.length - token.length;
                    return {
                        query,
                        token,
                        start,
                        end: start + token.length,
                    };
                },
                getSelectionIndices() {
                    const selection = window.getSelection();
                    if (!selection || !selection.rangeCount) {
                        const length = this.value.length;
                        return { start: length, end: length };
                    }
                    const range = selection.getRangeAt(0);
                    const preSelectionRange = range.cloneRange();
                    preSelectionRange.selectNodeContents(this.$refs.editor);
                    preSelectionRange.setEnd(range.startContainer, range.startOffset);
                    const start = this.htmlToPlainText(preSelectionRange.toString()).length;
                    const selectionRange = range.cloneRange();
                    const selectedText = selectionRange.toString();
                    const end = start + this.htmlToPlainText(selectedText).length;
                    return { start, end };
                },
                setCaretPosition(index) {
                    const node = this.$refs.editor;
                    const selection = window.getSelection();
                    if (!selection) return;
                    selection.removeAllRanges();
                    const range = document.createRange();
                    let current = 0;

                    const walker = document.createTreeWalker(node, NodeFilter.SHOW_TEXT, null);
                    let textNode = walker.nextNode();
                    let found = false;

                    while (textNode) {
                        const next = current + textNode.textContent.length;
                        if (index <= next) {
                            range.setStart(textNode, Math.max(0, index - current));
                            range.collapse(true);
                            found = true;
                            break;
                        }
                        current = next;
                        textNode = walker.nextNode();
                    }

                    if (!found) {
                        range.selectNodeContents(node);
                        range.collapse(false);
                    }

                    selection.addRange(range);
                },
                escapeHtml(value) {
                    const div = document.createElement('div');
                    div.textContent = value;
                    return div.innerHTML;
                },
                escapeAndFormat(value) {
                    return this.escapeHtml(value ?? '').replace(/\n/g, '<br>');
                },
                htmlToPlainText(html) {
                    if (!html) return '';
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    let text = temp.textContent || temp.innerText || '';
                    text = text.replace(/\u00A0/g, ' ');
                    text = text.replace(/\r\n?/g, '\n');
                    return text;
                },
            }));
        });
    </script>
@endPushOnce
