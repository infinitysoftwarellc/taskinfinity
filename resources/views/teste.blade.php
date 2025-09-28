<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clone – Layout de Tarefas</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <!-- Ícones Lucide via CDN -->
  <script defer src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
</head>
<body class="tasks-board">
  <div class="app">
    <!-- SIDEBAR -->
    <aside class="sidebar panel">
      <h6>Atalhos</h6>
      <nav>
        <ul class="nav-list">
          <li><a class="nav-item" href="#"><i class="icon" data-lucide="infinity"></i><span class="label">All</span><span class="count">38</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon" data-lucide="sun"></i><span class="label">Today</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon" data-lucide="calendar-days"></i><span class="label">Next 7 Days</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon" data-lucide="inbox"></i><span class="label">Inbox</span><span class="count">2</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon" data-lucide="pie-chart"></i><span class="label">Summary</span></a></li>
        </ul>
      </nav>

      <div class="sep"></div>

      <button class="workspace" aria-expanded="true" data-toggle="workspace">
        <i class="chev" data-lucide="chevron-down"></i>
        <span class="title">SOFTWAREINFINITY</span>
        <span class="badge">36</span>
      </button>
      <div class="workspace-content">
        <ul class="nav-list">
          <li><a class="nav-item" href="#"><i class="icon" data-lucide="list-todo"></i><span class="label">Tasks</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon" data-lucide="flame"></i><span class="label">Habits</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon" data-lucide="clock"></i><span class="label">Pomodoro</span></a></li>
        </ul>
      </div>

      <h6>Filters</h6>
      <div class="filters-tip">Display tasks filtered by list, date, priority, tag, and more</div>

      <h6>Tags</h6>
      <div class="tags">
        <a class="tag" href="#"><span class="dot dot--salmon"></span> <span>Bugs</span> <span class="count"></span></a>
        <a class="tag" href="#"><span class="dot dot--cyan"></span> <span>Melhorias</span></a>
      </div>

      <h6 class="heading-spaced"> </h6>
      <div class="completed"><i class="icon" data-lucide="check-square"></i> Completed</div>
    </aside>

    <!-- MAIN -->
    <main class="main panel">
      <div class="toolbar">
        <div class="title">All <span class="bubble">38</span></div>
        <div class="spacer"></div>
        <button class="icon-btn" title="Ordenar"><i data-lucide="sort-desc"></i></button>
        <button class="icon-btn" title="Opções"><i data-lucide="more-horizontal"></i></button>
      </div>

      <div class="add-row">
        <input class="add-input" placeholder="Add task to 'Inbox'" />
      </div>

      <section class="group" aria-expanded="true">
        <header class="group-header" data-toggle="group">
          <i class="chev" data-lucide="chevron-down"></i>
          <span class="group-title">No Date</span>
          <span class="group-count">38</span>
        </header>
        <div class="group-body">

          <div class="subgroup" aria-expanded="true">
            <div class="subgroup-toggle" data-toggle="subgroup">
              <i class="chev" data-lucide="chevron-down"></i>
              <span class="name">aa</span>
            </div>

            <div class="task-list">
              <!-- Linha fantasma "No Title" -->
              <div class="task ghost">
                <div class="checkbox" aria-hidden="true"></div>
                <div class="title-line"><span class="title title--ghost">No Title</span></div>
                <div class="meta">Inbox</div>
              </div>

              <!-- Tarefas (exemplo) -->
              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">CONVERTER TAREFAS DE UM TEMA PARA OUTRO</span></div>
                <div class="meta">Task Infinity</div>
              </div>

              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">COLOCAR METAS</span></div>
                <div class="meta">Task Infinity</div>
              </div>

              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">COLOCAR IA</span></div>
                <div class="meta">Task Infinity</div>
              </div>

              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">THEMA FLORESTAL</span></div>
                <div class="meta">Task Infinity</div>
              </div>

              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">THEMA GAMER</span></div>
                <div class="meta">Task Infinity</div>
              </div>

              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">ADICIONAR</span></div>
                <div class="meta">Task Infinity</div>
              </div>

              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">HABITOS</span></div>
                <div class="meta">Task Infinity</div>
              </div>

              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">POMODORO</span></div>
                <div class="meta">Task Infinity</div>
              </div>

              <div class="task">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="title-line"><span class="title">TUDO QUE FALTA</span></div>
                <div class="meta">Task Infinity</div>
              </div>

            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- DETAILS -->
    <aside class="details panel">
      <div class="header">
        <div class="details-breadcrumb">aa ›</div>
        <div class="right">
          <button class="icon-btn" title="Classificar por data"><i data-lucide="flag"></i></button>
          <button class="icon-btn" title="Opções"><i data-lucide="more-horizontal"></i></button>
        </div>
      </div>
      <div class="empty">
        <h3 class="empty-title">What would you like to do?</h3>
        <p>Selecione uma tarefa para ver os detalhes, adicionar notas, e muito mais.</p>
      </div>
    </aside>
  </div>

  <script>
    // Inicializa os ícones Lucide
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
    });

    // Toggle helpers
    function toggleSection(btn, content){
      const expanded = btn.getAttribute('aria-expanded') !== 'false';
      btn.setAttribute('aria-expanded', String(!expanded));
      content.style.display = expanded ? 'none' : '';
    }

    // Workspace collapse
    const wsBtn = document.querySelector('[data-toggle="workspace"]');
    const wsContent = document.querySelector('.workspace-content');
    wsBtn?.addEventListener('click', () => toggleSection(wsBtn, wsContent));

    // Group collapse
    document.querySelectorAll('[data-toggle="group"]').forEach(h => {
      h.addEventListener('click', () => {
        const section = h.closest('.group');
        const body = section.querySelector('.group-body');
        const expanded = section.getAttribute('aria-expanded') !== 'false';
        section.setAttribute('aria-expanded', String(!expanded));
        body.style.display = expanded ? 'none' : '';
      });
    });

    // Subgroup collapse
    document.querySelectorAll('[data-toggle="subgroup"]').forEach(h => {
      h.addEventListener('click', () => {
        const sg = h.closest('.subgroup');
        const list = sg.querySelector('.task-list');
        const expanded = sg.getAttribute('aria-expanded') !== 'false';
        sg.setAttribute('aria-expanded', String(!expanded));
        list.style.display = expanded ? 'none' : '';
      });
    });

    // Checkbox interactions
    document.querySelectorAll('.task .checkbox').forEach(cb => {
      cb.addEventListener('click', (e) => {
        e.stopPropagation();
        cb.classList.toggle('checked');
        cb.closest('.task').classList.toggle('done');
      });
    });

    // Demo: adicionar tarefa rapidamente com Enter
    const input = document.querySelector('.add-input');
    const taskList = document.querySelector('.task-list');
    input?.addEventListener('keydown', (e) => {
      if(e.key === 'Enter' && input.value.trim()){ 
        const row = document.createElement('div');
        row.className = 'task';
        row.innerHTML = `
          <button class="checkbox" aria-label="marcar"></button>
          <div class="title-line"><span class="title"></span></div>
          <div class="meta">Inbox</div>`;
        row.querySelector('.title').textContent = input.value.trim();
        row.querySelector('.checkbox').addEventListener('click', (ev)=>{
          ev.stopPropagation();
          row.classList.toggle('done');
          ev.currentTarget.classList.toggle('checked');
        });
        taskList.prepend(row);
        input.value='';
      }
    });
  </script>
</body>
</html>
