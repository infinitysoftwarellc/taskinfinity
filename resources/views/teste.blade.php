<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>To‑Do Layout (Clone)</title>
  <style>
    :root{
      --bg:#0f1115;
      --panel:#161a22;
      --panel-2:#12151c;
      --border:#1f2430;
      --hover:#1c2130;
      --text:#e7e9ee;
      --muted:#9aa3b2;
      --muted-2:#6d7585;
      --brand:#7aa2ff;
      --green:#2ecc71;
      --yellow:#ffcc66;
      --radius:18px;
    }
    *,*::before,*::after{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;background:var(--bg);color:var(--text);font:14px/1.35 system-ui}
    .app{
      display:grid;
      grid-template-columns:64px 280px 1fr 420px;
      grid-template-rows:1fr;
      grid-template-areas:"rail sidebar main details";
      height:100vh;
      gap:12px;
      padding:12px;
    }
    .rail{grid-area:rail;background:var(--panel-2);border:1px solid var(--border);border-radius:var(--radius);padding:8px;display:flex;flex-direction:column;align-items:center;gap:8px}
    .rail .avatar{width:36px;height:36px;border-radius:12px;background:linear-gradient(135deg,#7aa2ff,#a78bfa)}
    .rail .vsep{height:1px;width:100%;background:var(--border);margin:6px 0}
    .rail .rbtn{width:40px;height:40px;border-radius:12px;display:grid;place-items:center;color:var(--muted);cursor:pointer}
    .rail .rbtn:hover{background:var(--hover);color:var(--text)}
    .sidebar{grid-area:sidebar;background:var(--panel);border:1px solid var(--border);border-radius:var(--radius);padding:10px;overflow:auto}
    .section{margin-top:8px}
    .section h6{margin:12px 8px 6px;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:var(--muted-2)}
    .item{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:12px;color:var(--text);cursor:pointer}
    .item:hover{background:var(--hover)}
    .item .badge{margin-left:auto;color:#c8cfe0;font-size:12px;background:#0c1017;border:1px solid var(--border);padding:2px 6px;border-radius:999px}
    .list-header{display:flex;align-items:center;gap:8px;margin:10px 8px 4px;color:var(--muted)}
    .tag{display:inline-flex;align-items:center;gap:8px;padding:8px 10px;border-radius:12px;cursor:pointer}
    .tag:hover{background:var(--hover)}
    .tag .dot{width:8px;height:8px;border-radius:999px}
    .main{grid-area:main;background:var(--panel);border:1px solid var(--border);border-radius:var(--radius);display:flex;flex-direction:column;overflow:hidden}
    .main-header{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--border)}
    .title{display:flex;align-items:center;gap:10px}
    .title h2{margin:0;font-size:18px}
    .addbar{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
    .addbar input{flex:1;background:var(--panel-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:12px}
    .group{padding:10px 0}
    .group-head{display:flex;align-items:center;gap:8px;font-weight:600;color:var(--muted);padding:8px 16px;cursor:pointer}
    .subgroup{padding-left:22px}
    .task{display:grid;grid-template-columns:28px 1fr 100px;align-items:center;gap:10px;padding:10px 16px;cursor:pointer}
    .task:hover{background:var(--hover)}
    .checkbox{width:18px;height:18px;border-radius:6px;border:1px solid var(--border);background:#0b0f15}
    .task .titleline{display:flex;align-items:center;gap:10px;color:#cfd6e6}
    .task .meta{justify-self:end;color:var(--muted-2)}
    .details{grid-area:details;background:var(--panel);border:1px solid var(--border);border-radius:var(--radius);display:flex;flex-direction:column}
    .details-header{display:flex;align-items:center;gap:10px;padding:12px 14px;border-bottom:1px solid var(--border)}
    .pill{border:1px solid var(--border);padding:6px 10px;border-radius:999px;color:#c8cfe0}
    .details-body{flex:1;padding:18px;color:#cfd6e6}
    .details-footer{padding:12px 14px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);color:var(--muted)}
    .chev{transition:transform .2s ease}
    .rot{-webkit-transform:rotate(-90deg);transform:rotate(-90deg)}
    ::-webkit-scrollbar{height:10px;width:10px}
    ::-webkit-scrollbar-thumb{background:#202637;border-radius:10px}
    ::-webkit-scrollbar-track{background:transparent}
    @media (max-width:1100px){.app{grid-template-columns:64px 260px 1fr}.details{display:none}}
  </style>
</head>
<body>
  <div class="app">
    <aside class="rail">
      <div class="avatar" title="Perfil"></div>
      <div class="vsep"></div>
      <button class="rbtn">📥</button>
      <button class="rbtn">📅</button>
      <button class="rbtn">🗂️</button>
      <button class="rbtn">🏷️</button>
      <div class="vsep"></div>
      <button class="rbtn">⚙️</button>
    </aside>
    <aside class="sidebar">
      <div class="section">
        <div class="item"><span>🏷️</span><span>All</span><span class="badge">38</span></div>
        <div class="item"><span>📅</span><span>Today</span><span class="badge">1</span></div>
        <div class="item"><span>🗓️</span><span>Next 7 Days</span><span class="badge">1</span></div>
        <div class="item"><span>📥</span><span>Inbox</span><span class="badge">2</span></div>
        <div class="item"><span>🧾</span><span>Summary</span></div>
      </div>
      <div class="section">
        <div class="list-header"><svg width="14" height="14" viewBox="0 0 24 24" class="chev rot"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" fill="none"/></svg> <strong>Lists</strong></div>
        <div class="item"><span>📁</span><span>SOFTWAREINFINITY</span><span class="badge">36</span></div>
      </div>
      <div class="section"><h6>Filters</h6><div class="item" style="color:var(--muted)">Display tasks filtered by list, date, priority, tag, and more</div></div>
      <div class="section"><h6>Tags</h6><div class="tag"><span class="dot" style="background:var(--green)"></span> Bugs</div><div class="tag"><span class="dot" style="background:var(--yellow)"></span> Melhorias</div></div>
      <div class="section"><h6>Outros</h6><div class="item"><span>✅</span><span>Completed</span></div></div>
    </aside>
    <main class="main">
      <div class="main-header"><div class="title"><h2>All</h2><span class="badge">38</span></div></div>
      <div class="addbar"><span>＋</span><input placeholder="Add task to \"Inbox\"" /></div>
      <div class="group" id="grp-nodate">
        <div class="group-head" data-toggle="#grp-nodate-list"><svg width="14" height="14" viewBox="0 0 24 24" class="chev"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" fill="none"/></svg><span>No Date</span> <span style="opacity:.6">38</span></div>
        <div class="subgroup" id="grp-nodate-list">
          <div class="group-head" data-toggle="#sg-aa-list" style="font-weight:500;color:#cfd6e6"><svg width="14" height="14" viewBox="0 0 24 24" class="chev rot"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" fill="none"/></svg><span>aa</span></div>
          <div id="sg-aa-list">
            <div class="task" data-title="aa"><div class="checkbox"></div><div class="titleline"><span class="muted">No Title</span></div><div class="meta">Inbox</div></div>
            <div class="task" data-title="CONVERTER TAREFAS DE UM TEMA PARA OUTRO"><div class="checkbox"></div><div class="titleline">CONVERTER TAREFAS DE UM TEMA PARA OUTRO</div><div class="meta">Task Infinity</div></div>
            <div class="task" data-title="COLOCAR METAS"><div class="checkbox"></div><div class="titleline">COLOCAR METAS</div><div class="meta">Task Infinity</div></div>
            <div class="task" data-title="COLOCAR IA"><div class="checkbox"></div><div class="titleline">COLOCAR IA</div><div class="meta">Task Infinity</div></div>
          </div>
        </div>
      </div>
    </main>
    <aside class="details" id="details">
      <div class="details-header"><span class="pill">Due Date</span></div>
      <div class="details-body" id="detailsBody"><h3 id="detailsTitle">aa</h3><p style="color:var(--muted)">What would you like to do?</p></div>
      <div class="details-footer"><span>📁 Inbox</span><span>🕒</span></div>
    </aside>
  </div>
  <script>
    document.querySelectorAll('[data-toggle]').forEach(el=>{el.addEventListener('click',()=>{const target=document.querySelector(el.dataset.toggle);const icon=el.querySelector('.chev');if(!target)return;const isHidden=target.style.display==='none';target.style.display=isHidden?'':'none';if(icon){icon.classList.toggle('rot',!isHidden);}})});
    const detailsTitle=document.getElementById('detailsTitle');const details=document.getElementById('details');document.querySelectorAll('.task').forEach(row=>{row.addEventListener('click',()=>{const title=row.getAttribute('data-title')||'Sem título';detailsTitle.textContent=title;document.getElementById('detailsBody').querySelector('p').textContent='What would you like to do?';details.style.display='';document.querySelectorAll('.task').forEach(t=>t.style.background='');row.style.background=getComputedStyle(document.documentElement).getPropertyValue('--hover');})});
    document.querySelectorAll('.task .checkbox').forEach(cb=>{cb.addEventListener('click',(e)=>{e.stopPropagation();cb.style.background=cb.style.background?'':'linear-gradient(135deg,#7aa2ff,#a78bfa)';cb.style.borderColor=cb.style.background?'transparent':'var(--border)';})});
  </script>
</body>
</html>
