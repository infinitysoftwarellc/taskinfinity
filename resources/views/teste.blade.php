<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clone – Layout de Tarefas</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="https://kit.fontawesome.com/c9cfb44e99.js" crossorigin="anonymous"></script>
  <style>
    :root{
      --bg: #0b0d12;           /* fundo app */
      --panel: #12151c;        /* cartões/painéis */
      --panel-2: #0f1218;      /* painéis mais escuros */
      --hover: #181c25;        /* hover rows */
      --border: #1f2430;       /* borda translúcida */
      --text: #e7ebf3;         /* texto principal */
      --muted: #97a2b2;        /* texto secundário */
      --muted-2:#6d7585;
      --accent: #7aa2ff;       /* azul contadores */
      --brand: #7aa2ff;        
      --success:#2ecc71;
      --warning:#ffcc66;
      --radius: 16px;
      --shadow: 0 10px 30px -12px rgba(0,0,0,.55);
    }

    *{ box-sizing: border-box; }
    html, body{ height:100%; }
    body{
      margin:0; background:var(--bg); color:var(--text);
      font: 14px/1.4 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, 'Helvetica Neue', Arial, 'Apple Color Emoji','Segoe UI Emoji';
    }

    .app{ 
      height:100vh; padding:12px; gap:12px; 
      display:grid; grid-template-columns: 64px 280px 1fr 420px; grid-template-rows: 1fr; 
      grid-template-areas: 'rail sidebar main details';
    }

    /* Painéis base */
    .panel{ background:var(--panel); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); }

    /* RAIL (menu lateral fino) */
    .rail{ grid-area: rail; background: var(--panel-2); border:1px solid var(--border); border-radius: var(--radius); padding:8px 6px; display:flex; flex-direction:column; align-items:center; gap:10px; }
    .rail .avatar{ width:36px; height:36px; border-radius:12px; background:linear-gradient(135deg,#7aa2ff,#a78bfa); box-shadow: 0 0 0 2px #10141c; }
    .rail .btn{ width:40px; height:40px; border-radius:12px; display:grid; place-items:center; border:1px solid var(--border); color:var(--muted); background:transparent; cursor:pointer; }
    .rail .btn:hover{ background:var(--hover); color:var(--text); }
    .rail .spacer{ flex:1; }

    /* Sidebar */
    .sidebar{ grid-area:sidebar; padding:12px; overflow:auto; }
    .sidebar h6{ margin:14px 8px 6px; font-size:11px; text-transform:uppercase; letter-spacing:.14em; color:var(--muted); font-weight:700; }
    .nav-list{ list-style:none; margin:6px 0 10px; padding:0; }

    .nav-item{ display:flex; align-items:center; gap:10px; padding:10px 10px; border-radius:12px; color:var(--text); text-decoration:none; }
    .nav-item:hover{ background:var(--hover); }
    .nav-item .icon{ width:18px; height:18px; color:var(--muted); }
    .nav-item .label{ flex:1; }
    .nav-item .count{ font-size:12px; font-weight:700; color:#cfe0ff; background: rgba(122,162,255,.12); border:1px solid rgba(122,162,255,.3); padding:.15rem .45rem; border-radius:999px; }

    .workspace{ display:flex; align-items:center; gap:8px; padding:8px 10px; cursor:pointer; border-radius:10px; }
    .workspace:hover{ background:var(--hover); }
    .workspace .chev{ width:16px; height:16px; color:var(--muted); transition: transform .2s ease; }
    .workspace[aria-expanded="false"] .chev{ transform: rotate(-90deg); }
    .workspace .title{ font-weight:600; font-size:13px; color:var(--text); }
    .workspace .badge{ margin-left:auto; font-size:12px; color:#cfe0ff; background: rgba(122,162,255,.12); border:1px solid rgba(122,162,255,.3); padding:.15rem .45rem; border-radius:999px; }

    .filters-tip{ background:var(--panel-2); border:1px dashed var(--border); color:var(--muted); padding:10px; border-radius:12px; font-size:12px; line-height:1.3; }

    .tags{ display:flex; flex-direction:column; gap:6px; }
    .tag{ display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:10px; color:var(--text); text-decoration:none; }
    .tag:hover{ background:var(--hover); }
    .dot{ width:8px; height:8px; border-radius:999px; }

    .completed{ display:flex; align-items:center; gap:10px; padding:10px; border-radius:10px; color:var(--muted); }
    .completed:hover{ background:var(--hover); color:var(--text); }

    /* Main */
    .main{ grid-area:main; overflow:auto; display:flex; flex-direction:column; }
    .toolbar{ display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--border); }
    .title{ font-size:20px; font-weight:700; letter-spacing:.01em; }
    .title .bubble{ margin-left:8px; font-size:12px; font-weight:700; color:#cfe0ff; background: rgba(122,162,255,.12); border:1px solid rgba(122,162,255,.3); padding:.15rem .5rem; border-radius:999px; }
    .toolbar .spacer{ flex:1; }
    .icon-btn{ display:inline-grid; place-items:center; width:28px; height:28px; border-radius:8px; border:1px solid var(--border); background:transparent; color:var(--muted); cursor:pointer; }
    .icon-btn:hover{ background:var(--hover); color:var(--text); }

    .add-row{ padding:10px 16px; border-bottom:1px solid var(--border); }
    .add-input{ width:100%; border:1px solid var(--border); background:var(--panel-2); color:var(--text); border-radius:12px; padding:10px 12px; outline:none; }
    .add-input::placeholder{ color:var(--muted); }

    .group{ padding:12px 0; }
    .group-header{ display:flex; align-items:center; gap:10px; padding:6px 16px; user-select:none; cursor:pointer; color:var(--muted); font-weight:700; text-transform:none; }
    .group-header .chev{ width:18px; height:18px; color:var(--muted-2); transition: transform .2s ease; }
    .group[aria-expanded="false"] .chev{ transform: rotate(-90deg); }
    .group-title{ font-size:12px; letter-spacing:.04em; text-transform:none; color:var(--muted); }
    .group-count{ margin-left:6px; color:#cfe0ff; font-size:12px; background: rgba(122,162,255,.12); border:1px solid rgba(122,162,255,.3); padding:.15rem .45rem; border-radius:999px; }

    .subgroup{ margin:4px 0 8px; }
    .subgroup-toggle{ display:flex; align-items:center; gap:10px; padding:8px 16px; cursor:pointer; color:var(--text); }
    .subgroup .chev{ width:16px; height:16px; color:var(--muted); transition: transform .2s ease; }
    .subgroup[aria-expanded="false"] .chev{ transform: rotate(-90deg); }
    .subgroup .name{ font-weight:600; }

    .task-list{ display:flex; flex-direction:column; }
    .task{ display:grid; grid-template-columns: 28px 1fr auto; align-items:center; gap:10px; padding:8px 16px; }
    .task:hover{ background:var(--hover); }
    .checkbox{ width:16px; height:16px; border-radius:4px; background:transparent; border:1px solid var(--border); position:relative; cursor:pointer; }
    .checkbox.checked{ background:linear-gradient(135deg, var(--brand), #a78bfa); border-color:transparent; }
    .checkbox.checked::after{ content:""; position:absolute; inset:2px 4px 4px 2px; border-right:2px solid white; border-bottom:2px solid white; transform: rotate(40deg); }

    .task .title-line{ display:flex; align-items:center; gap:8px; }
    .task .title-line .title{ font-size:14px; font-weight:600; }
    .task .meta{ color:var(--muted); font-size:12px; }

    .task.done .title{ color:var(--muted); text-decoration:line-through; font-weight:500; }

    /* Subtarefas */
    .task.has-subtasks{ grid-template-columns: 28px 16px 1fr auto; }
    .expander{ width:16px; height:16px; display:grid; place-items:center; color:var(--muted); cursor:pointer; }
    .expander svg{ width:16px; height:16px; transition: transform .2s ease; }
    .task[aria-expanded="false"] .expander svg{ transform: rotate(-90deg); }

    .subtasks{ padding:0 16px 4px 60px; display:flex; flex-direction:column; gap:4px; }
    .subtasks .subtask{ display:grid; grid-template-columns: 16px 1fr auto; align-items:center; gap:8px; padding:6px 0; }
    .subtasks .checkbox{ width:14px; height:14px; border-radius:3px; }
    .subtasks .title{ font-size:13px; font-weight:500; }
    .subtasks .meta{ font-size:11px; color:var(--muted); }
    .subtask.done .title{ text-decoration: line-through; color: var(--muted); }
    .add-subtask{ margin:6px 0 8px; display:flex; gap:8px; align-items:center; }
    .add-subtask input{ flex:1; border:1px solid var(--border); background:var(--panel-2); color:var(--text); border-radius:10px; padding:8px 10px; outline:none; font-size:13px; }

    /* Details */
    .details{ grid-area:details; overflow:auto; display:flex; flex-direction:column; }
    .details .header{ display:flex; align-items:center; gap:8px; padding:14px 16px; border-bottom:1px solid var(--border); }
    .details .header .right{ margin-left:auto; display:flex; gap:8px; }

    .empty{ padding:20px 16px; color:var(--muted); }

    /* Util */
    .sep{ height:1px; background:var(--border); margin:10px 0; }

    /* Scrollbar bonitinha (navegadores WebKit) */
    *::-webkit-scrollbar{ width:10px; height:10px; }
    *::-webkit-scrollbar-thumb{ background:#1b2230; border-radius:10px; border:2px solid #0e1219; }
    *::-webkit-scrollbar-track{ background:transparent; }

    /* Responsivo */
    @media (max-width: 1200px){ .app{ grid-template-columns: 64px 240px 1fr 360px; } }
    @media (max-width: 980px){ .app{ grid-template-columns: 64px 220px 1fr; grid-template-areas:'rail sidebar main' 'rail sidebar details'; grid-template-rows: 55% 45%; } }
    @media (max-width: 720px){ .app{ grid-template-columns:1fr; grid-template-areas:'main'; } .sidebar, .details, .rail{ display:none; } }
  </style>
</head>
<body>
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
      <div class="workspace-content" style="padding-left:8px;">
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
        <a class="tag" href="#"><span class="dot" style="background:#f87171"></span> <span>Bugs</span> <span class="count" style="margin-left:auto"> </span></a>
        <a class="tag" href="#"><span class="dot" style="background:#22d3ee"></span> <span>Melhorias</span></a>
      </div>

      <h6 style="margin-top:14px"> </h6>
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
              <span class="meta" style="margin-left:auto; color:var(--muted)">Inbox</span>
            </div>

            <div class="task-list">
              <!-- Linha fantasma "No Title" -->
              <div class="task ghost">
                <div class="checkbox" aria-hidden="true"></div>
                <div class="title-line"><span class="title" style="opacity:.6">No Title</span></div>
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
        <div style="font-weight:700; color:var(--muted)">aa ›</div>
        <div class="right">
          <button class="icon-btn" title="Classificar por data"><i class="fa-solid fa-flag" aria-hidden="true"></i></button>
          <button class="icon-btn" title="Opções"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></button>
        </div>
      </div>
      <div class="empty">
        <h3 style="margin:6px 0 6px; font-size:18px; color:#d7def0">What would you like to do?</h3>
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
