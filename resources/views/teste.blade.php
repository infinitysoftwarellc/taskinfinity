<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Task UI Clone</title>
  <!-- Roboto -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0f1216;        /* page background */
      --panel: #13171c;     /* panels */
      --panel-2: #151a21;   /* panel hover/alt */
      --muted: #8b96a5;     /* secondary text */
      --text: #e8eef5;      /* primary text */
      --primary: #3b82f6;   /* blue accents */
      --accent: #8b5cf6;    /* purple accent */
      --border: #212832;    /* subtle borders */
      --green: #22c55e;     /* success */
      --yellow: #eab308;    /* warning/dot */
      --shadow: 0 10px 30px rgba(0,0,0,.35);
      --radius: 14px;
      --rail: #0c0f13;      /* left rail */
    }

    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0; font: 16px/1.55 Roboto, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, "Helvetica Neue", Arial;
      color: var(--text); background: radial-gradient(1200px 800px at 20% 0%, #0d1116 15%, #0a0c10 60%) fixed, var(--bg);
    }

    /* --- Layout (added left rail menu) --- */
    .app {
      height: 100%;
      display: grid;
      grid-template-columns: 64px 300px 1.3fr 360px; /* maior área de tarefas */
      gap: 16px;
      padding: 16px;
    }

    .panel { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
    .panel-header { padding: 14px 16px; border-bottom: 1px solid var(--border); display:flex; align-items:center; gap:10px; }
    .panel-title { font-weight: 700; letter-spacing: .2px; }

    /* --- Left Rail (menu lateral) --- */
    .rail { background: var(--rail); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); display:flex; flex-direction:column; align-items:center; padding:10px 6px; gap:12px; }
    .rail .brand { width: 36px; height: 36px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--accent)); display:grid; place-items:center; font-weight:700; }
    .rail .rbtn { width: 42px; height: 42px; border-radius: 12px; display:grid; place-items:center; color: var(--muted); cursor:pointer; border: 1px solid transparent; }
    .rail .rbtn:hover, .rail .rbtn.active { background: var(--panel-2); color: var(--text); border-color: var(--border); }
    .icon { width: 20px; height: 20px; opacity:.95; }
    .rail .spacer { flex:1; }

    /* --- Sidebar Left --- */
    .sidebar { display:flex; flex-direction:column; }

    .sb-scroll { padding: 10px; height: calc(100% - 52px); overflow: auto; }
    /* scroll invisível mas funcional */
    .sb-scroll::-webkit-scrollbar { width: 0; height: 0; }
    .sb-scroll { scrollbar-width: none; }

    .nav-item { display:flex; align-items:center; gap:12px; padding:12px 12px; border-radius: 12px; color: var(--text); text-decoration:none; border:1px solid transparent; font-weight:500; }
    .nav-item:hover { background: var(--panel-2); border-color: var(--border); }
    .nav-item .count { margin-left:auto; background:#0d1116; border:1px solid var(--border); color: var(--muted); padding:2px 8px; border-radius:999px; font-size:12px; }

    .sb-heading { color: var(--muted); font-size: 12px; letter-spacing:.4px; padding:10px 12px; text-transform: uppercase; font-weight:700; }

    .list-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius: 12px; cursor:pointer; }
    .list-item:hover { background: var(--panel-2); }

    .dot { width: 10px; height: 10px; border-radius: 50%; background: var(--yellow); box-shadow: 0 0 0 2px #0d1116; }

    .mini { color: var(--muted); font-size:13px; }

    .footer { border-top:1px solid var(--border); padding: 10px 12px; display:flex; justify-content:space-between; color:var(--muted); }

    /* --- Middle: List (maior tipografia e área) --- */
    .list-panel { display:flex; flex-direction:column; }

    .addbar { padding: 12px 16px; }
    .add-input { width:100%; background:#0d1116; border:1px solid var(--border); color: var(--text); height:48px; border-radius: 14px; padding: 0 14px; outline:none; font-size:16px; }
    .add-input::placeholder { color: #7b8492; }

    .group { border-top:1px solid var(--border); }
    .group-header { padding: 14px 18px; display:flex; align-items:center; gap:10px; cursor:pointer; }
    .group-header .chev { transition: transform .2s ease; opacity:.9; font-size:18px; }
    .group.collapsed .chev { transform: rotate(-90deg); }
    .group-title { font-weight: 700; font-size: 18px; }
    .group-count { color: var(--muted); font-size: 13px; margin-left: 6px; }

    .tasks { display:flex; flex-direction:column; gap:8px; padding: 6px 12px 18px 12px; }

    .task { display:grid; grid-template-columns: 30px 1fr 110px; align-items:center; gap:14px; padding: 12px 10px 12px 8px; margin: 0 6px; border-radius: 12px; }
    .task:hover { background: var(--panel-2); }
    .title { font-size: 16px; font-weight:500; }

    .checkbox { width: 20px; height: 20px; border-radius: 6px; border:1px solid var(--border); display:inline-grid; place-items:center; background:#0d1116; cursor:pointer; }
    .checkbox svg { opacity:0; transform: scale(.5); transition: .16s ease; }
    .task.done .checkbox { background: rgba(59,130,246,.12); border-color: var(--primary); }
    .task.done .checkbox svg { opacity:1; transform: scale(1); }
    .task.done .title { color: var(--muted); text-decoration: line-through; }

    .pill { justify-self:end; font-size: 13px; padding: 6px 10px; border:1px solid var(--border); color: var(--muted); border-radius: 999px; }

    .see-more { color: var(--muted); padding: 8px 18px 14px 50px; cursor:pointer; font-weight:500; }
    .see-more:hover { color: var(--text); }

    /* --- Right: Details --- */
    .details { display:flex; flex-direction:column; }
    .details-header { padding: 14px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
    .meta { display:flex; align-items:center; gap:10px; color: var(--muted); font-size: 14px; }

    .details-body { padding: 18px; overflow:auto; }
    .title-edit { width:100%; background:transparent; border:none; color:var(--text); font-size: 24px; font-weight: 700; outline:none; padding: 6px 0; }
    .desc { min-height: 140px; background:#0d1116; border:1px solid var(--border); border-radius: 14px; padding: 14px; color: var(--muted); font-size:15px; }

    .right-footer { margin-top:auto; border-top:1px solid var(--border); padding: 10px 12px; color: var(--muted); display:flex; align-items:center; gap:8px; }

    .ghost { opacity:.6; }

    /* Responsive */
    @media (max-width: 1200px) {
      .app { grid-template-columns: 64px 260px 1fr; }
      .details { display:none; }
    }
    @media (max-width: 760px) {
      .app { grid-template-columns: 64px 1fr; }
      .sidebar { display:none; }
    }
  </style>
</head>
<body>
  <div class="app">
    <!-- Left Rail Menu (novo) -->
    <nav class="rail">
      <div class="brand">TI</div>
      <div class="rbtn active" title="Início">🏠</div>
      <div class="rbtn" title="Hoje">📅</div>
      <div class="rbtn" title="7 dias">🗓️</div>
      <div class="rbtn" title="Inbox">📥</div>
      <div class="rbtn" title="Resumo">📈</div>
      <div class="spacer"></div>
      <div class="rbtn" title="Configurações">⚙️</div>
    </nav>

    <!-- Sidebar -->
    <aside class="panel sidebar">
      <div class="panel-header">
        <span class="panel-title">Listas</span>
      </div>
      <div class="sb-scroll">
        <div class="sb-section">
          <a class="nav-item" href="#"><span>📚</span> All <span class="count">1</span></a>
          <a class="nav-item" href="#"><span>📅</span> Today <span class="count">1</span></a>
          <a class="nav-item" href="#"><span>🗓️</span> Next 7 Days <span class="count">1</span></a>
          <a class="nav-item" href="#"><span>📥</span> Inbox <span class="count">1</span></a>
          <a class="nav-item" href="#"><span>📈</span> Summary</a>
        </div>
        <div class="sb-section">
          <div class="sb-heading">Lists</div>
          <div class="list-item"><span>▸</span> SOFTWAREINFINITY</div>
          <div class="list-item"><span>▸</span> teste</div>
          <div class="list-item"><span>▸</span> Task Infinity</div>
          <div class="list-item"><span>▸</span> PORTFOLIO</div>
          <!-- conteúdo extra para demonstrar scroll invisível -->
          <div class="list-item"><span>▸</span> Clientes</div>
          <div class="list-item"><span>▸</span> Marketing</div>
          <div class="list-item"><span>▸</span> Product Roadmap</div>
          <div class="list-item"><span>▸</span> Bugs & QA</div>
          <div class="list-item"><span>▸</span> Ideias</div>
          <div class="list-item"><span>▸</span> Aprender</div>
          <div class="list-item"><span>▸</span> Backlog</div>
          <div class="list-item"><span>▸</span> Arquivo</div>
        </div>
        <div class="sb-section">
          <div class="sb-heading">Filters</div>
          <div class="mini">Display tasks filtered by list, date, priority, tag, and more</div>
        </div>
        <div class="sb-section">
          <div class="sb-heading">Tags</div>
          <div class="list-item"><div class="dot"></div> Bugs <span class="mini" style="margin-left:auto">•</span></div>
        </div>
      </div>
      <div class="footer mini">
        <span>⚙️ Settings</span>
        <span>Dark</span>
      </div>
    </aside>

    <!-- Middle Column -->
    <section class="panel list-panel">
      <div class="panel-header" style="justify-content:space-between;">
        <div class="panel-title">All</div>
        <div title="Ordenar" class="ghost">⇅</div>
      </div>
      <div class="addbar">
        <input id="addInput" class="add-input" placeholder="+ Adicionar tarefa à \"Inbox\"" />
      </div>

      <!-- Group: No Date -->
      <div class="group" id="group-no-date">
        <div class="group-header" data-toggle="#group-no-date">
          <div class="chev">▾</div>
          <div class="group-title">No Date <span class="group-count" id="noDateCount">1</span></div>
        </div>
        <div class="tasks">
          <div class="task" data-id="t-1">
            <div class="checkbox" role="checkbox" aria-checked="false" tabindex="0"> <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></div>
            <div class="title">aa</div>
            <div class="pill">Inbox</div>
          </div>
        </div>
      </div>

      <!-- Group: Completed -->
      <div class="group" id="group-completed">
        <div class="group-header" data-toggle="#group-completed">
          <div class="chev">▾</div>
          <div class="group-title">Completed <span class="group-count">5</span></div>
        </div>
        <div class="tasks" id="completedTasks">
          <div class="task done" data-id="t-2">
            <div class="checkbox" role="checkbox" aria-checked="true" tabindex="0"> <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></div>
            <div class="title">2 - estrutura de rotas e componentes</div>
            <div class="pill">Task Infinity</div>
          </div>
          <div class="task done" data-id="t-3">
            <div class="checkbox" role="checkbox" aria-checked="true" tabindex="0"> <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></div>
            <div class="title">1 - configurar o banco de dados</div>
            <div class="pill">Task Infinity</div>
          </div>
        </div>
        <div class="see-more" id="seeMore">View more</div>
      </div>
    </section>

    <!-- Right Column -->
    <aside class="panel details" id="details">
      <div class="details-header">
        <div class="meta"><span>☑️</span> <span>|</span> <span>Due Date</span></div>
      </div>
      <div class="details-body">
        <input class="title-edit" id="detailTitle" value="aa" />
        <div class="desc" id="detailDesc" contenteditable="true">Escreva detalhes, anexos, checklists…</div>
      </div>
      <div class="right-footer mini">
        <span>📥 Inbox</span>
      </div>
    </aside>
  </div>

  <script>
    // Utility: qsa/qs
    const qs = (s, el=document) => el.querySelector(s);
    const qsa = (s, el=document) => Array.from(el.querySelectorAll(s));

    // Toggle groups (collapse/expand)
    qsa('.group-header').forEach(h => {
      h.addEventListener('click', () => {
        const id = h.getAttribute('data-toggle');
        const group = qs(id);
        group.classList.toggle('collapsed');
        const tasks = group.querySelector('.tasks');
        tasks.style.display = group.classList.contains('collapsed') ? 'none' : 'flex';
      });
    });

    // Task check / uncheck + selection
    qsa('.task').forEach(task => {
      const cb = qs('.checkbox', task);
      cb.addEventListener('click', (e) => {
        e.stopPropagation();
        task.classList.toggle('done');
        const checked = task.classList.contains('done');
        cb.setAttribute('aria-checked', String(checked));
      });

      task.addEventListener('click', () => selectTask(task));
    });

    function selectTask(task){
      qsa('.task').forEach(t => t.classList.remove('active'));
      task.classList.add('active');
      const title = qs('.title', task)?.textContent?.trim() || '';
      qs('#detailTitle').value = title;
    }

    // Add new tasks (enter)
    const addInput = qs('#addInput');
    const noDateCount = qs('#noDateCount');
    addInput.addEventListener('keydown', (e) => {
      if(e.key === 'Enter' && addInput.value.trim()){
        const t = document.createElement('div');
        t.className = 'task';
        t.innerHTML = `
          <div class="checkbox" role="checkbox" aria-checked="false" tabindex="0">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
          </div>
          <div class="title"></div>
          <div class="pill">Inbox</div>`;
        qs('.title', t).textContent = addInput.value.trim();
        t.querySelector('.checkbox').addEventListener('click', (ev)=>{
          ev.stopPropagation();
          t.classList.toggle('done');
          const checked = t.classList.contains('done');
          ev.currentTarget.setAttribute('aria-checked', String(checked));
        });
        t.addEventListener('click', ()=>selectTask(t));
        qs('#group-no-date .tasks').prepend(t);

        // update count
        const current = Number(noDateCount.textContent || '0');
        noDateCount.textContent = current + 1;
        addInput.value = '';
      }
    });

    // View more completed demo
    qs('#seeMore')?.addEventListener('click', () => {
      const wrap = qs('#completedTasks');
      for(let i=0; i<3; i++){
        const n = document.createElement('div');
        n.className = 'task done';
        n.innerHTML = `
          <div class="checkbox" role="checkbox" aria-checked="true" tabindex="0">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
          </div>
          <div class="title">Tarefa concluída extra</div>
          <div class="pill">Task Infinity</div>`;
        wrap.appendChild(n);
      }
      qs('#seeMore').remove();
    });

    // Sync title editor to list item
    qs('#detailTitle').addEventListener('input', (e) => {
      const active = qs('.task.active');
      if(active) qs('.title', active).textContent = e.target.value;
    });
  </script>
</body>
</html>
