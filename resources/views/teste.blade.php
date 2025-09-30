<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clone – Layout de Tarefas</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="https://kit.fontawesome.com/c9cfb44e99.js" crossorigin="anonymous"></script>
  @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="page-teste">
  <div class="app">
    <!-- RAIL (menu lateral fino) -->
    <aside class="rail">
      <div class="avatar" title="Você"></div>
      <button class="btn" title="All"><i class="fa-solid fa-list-check" aria-hidden="true"></i></button>
      <button class="btn" title="Today"><i class="fa-solid fa-sun" aria-hidden="true"></i></button>
      <button class="btn" title="7 Days"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></button>
      <button class="btn" title="Inbox"><i class="fa-solid fa-inbox" aria-hidden="true"></i></button>
      <button class="btn" title="Summary"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i></button>
      <div class="spacer"></div>
      <button class="btn" title="Settings"><i class="fa-solid fa-gear" aria-hidden="true"></i></button>
    </aside>

    <!-- SIDEBAR -->
    <aside class="sidebar panel">
      <h6>Atalhos</h6>
      <nav>
        <ul class="nav-list">
          <li><a class="nav-item" href="#"><i class="icon fa-solid fa-infinity" aria-hidden="true"></i><span class="label">All</span><span class="count">38</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon fa-solid fa-sun" aria-hidden="true"></i><span class="label">Today</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon fa-solid fa-calendar-days" aria-hidden="true"></i><span class="label">Next 7 Days</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon fa-solid fa-inbox" aria-hidden="true"></i><span class="label">Inbox</span><span class="count">2</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon fa-solid fa-chart-pie" aria-hidden="true"></i><span class="label">Summary</span></a></li>
        </ul>
      </nav>

      <div class="sep"></div>

      <button class="workspace" aria-expanded="true" data-toggle="workspace">
        <i class="chev fa-solid fa-chevron-down" aria-hidden="true"></i>
        <span class="title">SOFTWAREINFINITY</span>
        <span class="badge">36</span>
      </button>
      <div class="workspace-content">
        <ul class="nav-list">
          <li><a class="nav-item" href="#"><i class="icon fa-solid fa-list-check" aria-hidden="true"></i><span class="label">Tasks</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon fa-solid fa-fire" aria-hidden="true"></i><span class="label">Habits</span></a></li>
          <li><a class="nav-item" href="#"><i class="icon fa-solid fa-clock" aria-hidden="true"></i><span class="label">Pomodoro</span></a></li>
        </ul>
      </div>

      <h6>Filters</h6>
      <div class="filters-tip">Display tasks filtered by list, date, priority, tag, and more</div>

      <h6>Tags</h6>
      <div class="tags">
        <a class="tag" href="#"><span class="dot dot--bug"></span> <span>Bugs</span> <span class="count"> </span></a>
        <a class="tag" href="#"><span class="dot dot--improvement"></span> <span>Melhorias</span></a>
      </div>

      <h6 class="sidebar-heading--spaced"> </h6>
      <div class="completed"><i class="icon fa-solid fa-square-check" aria-hidden="true"></i> Completed</div>
    </aside>

    <!-- MAIN -->
    <main class="main panel">
      <div class="toolbar">
        <div class="title">All <span class="bubble">38</span></div>
        <div class="spacer"></div>
        <button class="icon-btn" title="Ordenar"><i class="fa-solid fa-arrow-down-wide-short" aria-hidden="true"></i></button>
        <button class="icon-btn" title="Opções"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></button>
      </div>

      <div class="add-row">
        <input class="add-input" placeholder="Add task to 'Inbox'" />
      </div>

      <section class="group" aria-expanded="true">
        <header class="group-header" data-toggle="group">
          <i class="chev fa-solid fa-chevron-down" aria-hidden="true"></i>
          <span class="group-title">No Date</span>
          <span class="group-count">38</span>
        </header>
        <div class="group-body">

          <div class="subgroup" aria-expanded="true">
            <div class="subgroup-toggle" data-toggle="subgroup">
              <i class="chev fa-solid fa-chevron-down" aria-hidden="true"></i>
              <span class="name">aa</span>
              <span class="meta">Inbox</span>
            </div>

            <div class="task-list">
              <!-- Linha fantasma "No Title" -->
              <div class="task ghost">
                <div class="checkbox" aria-hidden="true"></div>
                <div class="title-line"><span class="title title--ghost">No Title</span></div>
                <div class="meta">Inbox</div>
              </div>

              <!-- Tarefa com SUBTAREFAS -->
              <div class="task has-subtasks" aria-expanded="true">
                <button class="checkbox" aria-label="marcar"></button>
                <div class="expander" title="Expandir/ocultar subtarefas"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></div>
                <div class="title-line"><span class="title">COLOCAR METAS</span></div>
                <div class="meta">Task Infinity</div>
              </div>
              <div class="subtasks">
                <div class="subtask">
                  <button class="checkbox"></button>
                  <div class="title">Definir metas trimestrais</div>
                  <div class="meta">Inbox</div>
                </div>
                <div class="subtask">
                  <button class="checkbox"></button>
                  <div class="title">Mapear KPIs por lista</div>
                  <div class="meta">Inbox</div>
                </div>
                <div class="add-subtask">
                  <i class="fa-solid fa-plus" aria-hidden="true"></i>
                  <input type="text" placeholder="Add subtask" class="add-subtask-input"/>
                </div>
              </div>

              <!-- Outras tarefas -->
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
          <button class="icon-btn" title="Classificar por data"><i class="fa-solid fa-flag" aria-hidden="true"></i></button>
          <button class="icon-btn" title="Opções"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></button>
        </div>
      </div>
      <div class="empty">
        <h3 class="empty-title">What would you like to do?</h3>
        <p>Selecione uma tarefa para ver os detalhes, adicionar notas, e muito mais.</p>
      </div>
    </aside>
  </div>

  <script>
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

    // Checkbox interactions (tarefas e subtarefas)
    function wireCheckbox(scope){
      scope.querySelectorAll('.checkbox').forEach(cb => {
        cb.addEventListener('click', (e) => {
          e.stopPropagation();
          cb.classList.toggle('checked');
          const row = cb.closest('.task, .subtask');
          row?.classList.toggle('done');
        });
      });
    }
    wireCheckbox(document);

    // Expander de subtarefas
    document.querySelectorAll('.task.has-subtasks .expander').forEach(exp => {
      exp.addEventListener('click', (e) => {
        const task = exp.closest('.task.has-subtasks');
        const next = task.nextElementSibling; // .subtasks logo abaixo
        const open = task.getAttribute('aria-expanded') !== 'false';
        task.setAttribute('aria-expanded', String(!open));
        if(next?.classList.contains('subtasks')){
          next.style.display = open ? 'none' : '';
        }
      });
    });

    // Adicionar subtask
    document.querySelectorAll('.add-subtask-input').forEach(input => {
      input.addEventListener('keydown', (e) => {
        if(e.key === 'Enter' && input.value.trim()){
          const container = input.closest('.subtasks');
          const node = document.createElement('div');
          node.className = 'subtask';
          node.innerHTML = `<button class="checkbox"></button><div class="title"></div><div class="meta">Inbox</div>`;
          node.querySelector('.title').textContent = input.value.trim();
          container.insertBefore(node, input.closest('.add-subtask'));
          input.value = '';
          wireCheckbox(node);
        }
      });
    });

    // Demo: adicionar tarefa com Enter
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
        wireCheckbox(row);
        taskList.prepend(row);
        input.value='';
      }
    });
  </script>
</body>
</html>
