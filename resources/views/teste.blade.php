<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Hábitos</title>
<style>
  :root{
    --bg:#0f1115;
    --panel:#141722;
    --panel-2:#0f131a;
    --muted:#7b8190;
    --text:#e9ecf1;
    --text-dim:#b9bfcc;
    --blue:#3772ff;
    --blue-2:#2b5de0;
    --green:#2fd17a;
    --yellow:#ffce3e;
    --red:#ff5c5c;
    --border:rgba(255,255,255,.06);
    --shadow:0 10px 30px rgba(0,0,0,.35);
    --radius-xl:22px;
    --radius-lg:18px;
    --radius-md:14px;
    --radius-sm:10px;
  }

  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0;
    background:linear-gradient(180deg,#0e1117 0%,#0b0d12 100%);
    color:var(--text);
    font: 14px/1.45 "Inter", system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, "Helvetica Neue", Arial, "Apple Color Emoji","Segoe UI Emoji";
  }

  /* ======= LAYOUT ======= */
  .app{
    display:grid;
    grid-template-columns: 260px 1fr 380px;
    gap:22px;
    padding:22px;
    min-height:100vh;
  }

  .sidebar{
    background:linear-gradient(180deg,#111522 0%, #0f121a 100%);
    border:1px solid var(--border);
    border-radius:var(--radius-xl);
    padding:18px;
    box-shadow:var(--shadow);
  }
  .sidebar .brand{
    display:flex; align-items:center; gap:12px;
    padding:10px 12px; border-radius:14px;
  }
  .brand .logo{
    width:34px;height:34px; border-radius:50%;
    display:grid; place-items:center;
    background:radial-gradient(120% 120% at 30% 20%, #4b82ff, #2d3b91);
    box-shadow: inset 0 2px 10px rgba(255,255,255,.06);
  }
  .muted{color:var(--muted)}
  .sidebar nav{margin-top:14px}
  .nav-group{margin-top:18px}
  .nav-label{
    text-transform:uppercase; letter-spacing:.18em;
    font-weight:700; font-size:11px; color:#97a0b8;
    margin:12px 10px 8px;
  }
  .nav-item{
    display:flex; align-items:center; gap:12px;
    padding:10px 12px; border-radius:12px; color:var(--text);
    text-decoration:none; transition: .2s ease;
    border:1px solid transparent;
  }
  .nav-item:hover{background:#141a28;border-color:var(--border)}
  .nav-item.active{background:#16213a;border-color:#22345f}
  .nav-item svg{opacity:.9}

  /* ======= MAIN ======= */
  .main{
    background:linear-gradient(180deg,#121623 0%, #0f131b 100%);
    border:1px solid var(--border);
    border-radius:var(--radius-xl);
    padding:18px 18px 24px;
    box-shadow:var(--shadow);
    overflow:hidden;
  }

  .main-header{
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:16px;
  }
  .main-title{font-size:20px; font-weight:700}
  .toolbar{display:flex; align-items:center; gap:10px}
  .btn{
    background:#111620; border:1px solid var(--border);
    color:var(--text); border-radius:12px;
    padding:8px 12px; display:flex; align-items:center; gap:8px;
    cursor:pointer; transition:.2s ease;
  }
  .btn:hover{background:#151b2a}
  .btn.primary{background:var(--blue); border-color:transparent}
  .btn.primary:hover{background:var(--blue-2)}

  /* Semana overview */
  .week-row{
    display:grid; grid-template-columns: repeat(7, 1fr);
    gap:22px; margin:8px 6px 18px;
  }
  .day-col{
    display:grid; justify-items:center; gap:8px;
    color:#9aa3b6;
  }
  .day-col .dow{font-size:12px}
  .day-col .date{
    width:26px;height:26px; display:grid; place-items:center;
    border-radius:50%;
    border:1px solid var(--border);
    background:#0f141f;
  }
  .check-dot{
    width:18px;height:18px; border-radius:50%;
    background:transparent;
    border:2px solid #2e3a53;
    display:inline-grid; place-items:center;
    cursor:pointer; transition:.2s;
  }
  .check-dot.checked{
    border-color:transparent; background:var(--blue);
    box-shadow:0 0 0 6px rgba(55,114,255,.15);
  }

  /* Card de Hábito */
  .habit-card{
    background:linear-gradient(180deg,#0f1320 0%, #0b0f18 100%);
    border:1px solid var(--border);
    border-radius:var(--radius-lg);
    padding:16px;
    display:grid; grid-template-columns: auto 1fr auto;
    gap:14px; align-items:center;
  }
  .avatar{
    width:40px;height:40px;border-radius:50%;
    display:grid;place-items:center;
    background:radial-gradient(120% 120% at 30% 20%, #ffe666, #f6b73d);
    color:#111; font-weight:800;
    border:1px solid rgba(255,255,255,.35);
  }
  .habit-info .name{
    font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .badges{display:flex; gap:10px; margin-top:6px; flex-wrap:wrap}
  .badge{
    display:inline-flex; align-items:center; gap:6px;
    font-size:12px; padding:6px 10px;
    border-radius:999px; border:1px solid var(--border);
    background:#0e1420; color:#b9c3d8;
  }
  .badge .spark{font-size:12px}
  .habit-actions{display:flex; gap:10px}

  /* Linhas de checks da semana dentro do card */
  .habit-checks{
    display:flex; gap:14px; margin-left:54px; margin-top:12px;
  }
  .habit-checks .tiny{
    width:16px;height:16px;border-radius:50%;
    border:2px solid #2c3750; cursor:pointer; transition:.2s;
  }
  .habit-checks .tiny.checked{background:var(--blue); border-color:transparent; box-shadow:0 0 0 4px rgba(55,114,255,.18)}

  /* ======= RIGHT PANE ======= */
  .right{
    background:linear-gradient(180deg,#121725 0%, #0e121a 100%);
    border:1px solid var(--border);
    border-radius:var(--radius-xl);
    padding:18px;
    box-shadow:var(--shadow);
    overflow:auto;
  }
  .right .title{
    font-size:18px; font-weight:800; margin:4px 0 14px;
  }

  .stats{
    display:grid; grid-template-columns: 1fr 1fr; gap:14px;
    margin-bottom:14px;
  }
  .stat{
    background:linear-gradient(180deg,#0f1524 0%, #0c111b 100%);
    border:1px solid var(--border);
    border-radius:var(--radius-md);
    padding:14px;
  }
  .stat .label{color:#9fb0cb; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.12em}
  .stat .value{font-size:26px; font-weight:800; margin-top:6px}
  .stat .sub{font-size:12px; color:#9aa3b6}

  /* Calendário mensal (direita) */
  .month{
    background:linear-gradient(180deg,#0f1527 0%, #0b1018 100%);
    border:1px solid var(--border); border-radius:var(--radius-md);
    padding:14px; margin-top:10px;
  }
  .month .bar{
    display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;
  }
  .month .bar .label{font-weight:800; color:#bfc8db}
  .cal{
    display:grid; gap:10px;
    grid-template-columns: repeat(7, 1fr);
    padding-top:6px;
  }
  .dow{font-size:12px; color:#8da0bf}
  .cell{
    height:44px; border-radius:12px;
    background:#0d1220; border:1px solid var(--border);
    display:grid; grid-template-rows: 1fr auto; place-items:center;
    cursor:pointer; transition:.15s;
  }
  .cell:hover{background:#121a31}
  .cell .num{font-size:13px; color:#9fb0cb; margin-top:6px}
  .cell .dot{
    width:12px;height:12px;border-radius:50%; margin-bottom:6px;
    background:transparent; border:2px solid #2c3750;
  }
  .cell.checked{box-shadow:0 0 0 6px rgba(55,114,255,.12) inset, 0 0 0 1px #28427a}
  .cell.checked .dot{background:var(--blue); border-color:transparent}
  .cell.today{outline:2px dashed rgba(255,255,255,.12); outline-offset:-3px}

  /* ======= Responsivo ======= */
  @media (max-width:1200px){
    .app{grid-template-columns: 220px 1fr 340px}
  }
  @media (max-width:980px){
    .app{grid-template-columns: 1fr}
    .right{order:3}
    .sidebar{order:1}
    .main{order:2}
  }

  /* Pequenos helpers de ícone */
  .icon{width:18px;height:18px; display:inline-block}
  .spacer{height:6px}
  hr.sep{border:0; border-top:1px solid var(--border); margin:14px 0}
</style>
</head>
<body>
  <div class="app">
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
      <div class="brand">
        <div class="logo">
          <!-- Lightning icon -->
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M13 2 L3 14 h7 l-1 8 L21 10 h-7 l-1-8z"/></svg>
        </div>
        <div>
          <div style="font-weight:800">Infinity Tasks</div>
          <div class="muted" style="font-size:12px">Seu ritual diário</div>
        </div>
      </div>

      <div class="nav-group">
        <div class="nav-label">Navegação</div>
        <a class="nav-item" href="#">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l9-9 9 9"/><path d="M9 21V9h6v12"/></svg>
          Início
        </a>
        <a class="nav-item active" href="#">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          Hábitos
        </a>
        <a class="nav-item" href="#">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H7l-4 3V5a2 2 0 0 1 2-2h10"/></svg>
          Tarefas
        </a>
        <a class="nav-item" href="#">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          Pomodoro
        </a>
      </div>

      <div class="nav-group">
        <div class="nav-label">Coleções</div>
        <a class="nav-item" href="#"><span class="icon" style="border-radius:6px;background:#1a2337"></span>Saúde</a>
        <a class="nav-item" href="#"><span class="icon" style="border-radius:6px;background:#1f283c"></span>Trabalho</a>
        <a class="nav-item" href="#"><span class="icon" style="border-radius:6px;background:#242e44"></span>Estudos</a>
      </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <main class="main">
      <div class="main-header">
        <div class="main-title">Habit</div>
        <div class="toolbar">
          <button class="btn">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3"/></svg>
            Buscar
          </button>
          <button class="btn primary" id="newHabitBtn">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Novo hábito
          </button>
        </div>
      </div>

      <!-- Linha do calendário semanal (visual do topo) -->
      <div class="week-row" id="weekRow">
        <!-- preenchido via JS conforme a semana corrente -->
      </div>

      <!-- Card do hábito -->
      <section class="habit-card" id="habitCard">
        <div class="avatar">😊</div>
        <div class="habit-info">
          <div class="name" id="habitTitle">Acredito Que Coisa Maravilhosas vão Acontecer</div>
          <div class="badges">
            <span class="badge"><span class="spark">⚡</span><span id="badgeTotal">7 Days</span></span>
            <span class="badge">⏳ <span id="badgeZero">0 Day</span></span>
          </div>
          <div class="habit-checks" id="habitWeekChecks">
            <!-- 7 bolinhas – semana corrente -->
          </div>
        </div>
        <div class="habit-actions">
          <button class="btn">Editar</button>
          <button class="btn">Arquivar</button>
        </div>
      </section>
    </main>

    <!-- ===== RIGHT PANE ===== -->
    <aside class="right">
      <div class="title" id="panelTitle">Acredito Que Coisa Maravilhosas vão Acontecer</div>

      <div class="stats">
        <div class="stat">
          <div class="label">Monthly check-ins</div>
          <div class="value" id="monthlyCount">7</div>
          <div class="sub" id="monthlyDays">Days</div>
        </div>
        <div class="stat">
          <div class="label">Total check-ins</div>
          <div class="value" id="totalCount">7</div>
          <div class="sub">Days</div>
        </div>
        <div class="stat">
          <div class="label">Monthly check-in rate</div>
          <div class="value"><span id="monthlyRate">25</span><span>%</span></div>
          <div class="sub">do mês atual</div>
        </div>
        <div class="stat">
          <div class="label">Current Streak</div>
          <div class="value"><span id="streakVal">0</span></div>
          <div class="sub">Day</div>
        </div>
      </div>

      <div class="month">
        <div class="bar">
          <button class="btn" id="prevMonth">‹</button>
          <div class="label" id="monthLabel">September 2025</div>
          <button class="btn" id="nextMonth">›</button>
        </div>
        <div class="cal" id="calHeader">
          <div class="dow">Sun</div><div class="dow">Mon</div><div class="dow">Tue</div>
          <div class="dow">Wed</div><div class="dow">Thu</div><div class="dow">Fri</div><div class="dow">Sat</div>
        </div>
        <div class="cal" id="calendar">
          <!-- células geradas no JS -->
        </div>
      </div>
    </aside>
  </div>

<script>
  // ======= Utilidades de data =======
  const pad = n => (n<10?'0':'')+n;
  const ymd = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
  const sameDay = (a,b)=> a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();

  // ======= Estado mockado (substitua por dados do backend) =======
  const state = {
    habitId: 'h1',
    name: 'Acredito Que Coisa Maravilhosas vão Acontecer',
    // armazenamos check-ins por data YYYY-MM-DD
    checks: new Set(),  // preenchido no carregamento com alguns exemplos
  };

  // Pré-popular alguns dias (simulando seu print)
  (function seed(){
    const today = new Date();
    const base = new Date(today.getFullYear(), today.getMonth(), 1);
    // Marca alguns dias dispersos
    [3,4,6,9,12,18,22,24,25].forEach(day=>{
      const d = new Date(base.getFullYear(), base.getMonth(), day);
      state.checks.add(ymd(d));
    });
    // Última semana alguns marcados
    for (let i=0;i<4;i++){
      const d = new Date(today); d.setDate(today.getDate() - (6-i));
      state.checks.add(ymd(d));
    }
  })();

  // ======= Semana (topo e card) =======
  function renderWeekRow(){
    const wrap = document.getElementById('weekRow');
    wrap.innerHTML='';
    const today = new Date();
    const start = new Date(today); start.setDate(today.getDate()-today.getDay()); // domingo
    const names = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    for(let i=0;i<7;i++){
      const d = new Date(start); d.setDate(start.getDate()+i);
      const col = document.createElement('div'); col.className='day-col';

      const dow = document.createElement('div'); dow.className='dow'; dow.textContent=names[i];
      const date = document.createElement('div'); date.className='date'; date.textContent=d.getDate();

      const dot = document.createElement('button'); dot.className='check-dot'; dot.title='Marcar dia';
      const key = ymd(d);
      if(state.checks.has(key)) dot.classList.add('checked');

      dot.addEventListener('click', ()=>{
        toggleCheck(d);
        dot.classList.toggle('checked');
        // também reflete no card semanal
        renderHabitWeekChecks();
        refreshStats();
        refreshCalendar(); // reflete no mensal
      });

      col.appendChild(dow); col.appendChild(date); col.appendChild(dot);
      wrap.appendChild(col);
    }
  }

  function renderHabitWeekChecks(){
    const wrap = document.getElementById('habitWeekChecks');
    wrap.innerHTML='';
    const today = new Date();
    const start = new Date(today); start.setDate(today.getDate()-today.getDay());
    for (let i=0;i<7;i++){
      const d = new Date(start); d.setDate(start.getDate()+i);
      const key = ymd(d);
      const dot = document.createElement('div'); dot.className='tiny';
      if(state.checks.has(key)) dot.classList.add('checked');
      dot.addEventListener('click', ()=>{
        toggleCheck(d);
        dot.classList.toggle('checked');
        // reflete no topo
        renderWeekRow();
        refreshStats();
        refreshCalendar();
      });
      wrap.appendChild(dot);
    }
  }

  function toggleCheck(date){
    const key = ymd(date);
    if(state.checks.has(key)) state.checks.delete(key);
    else state.checks.add(key);
  }

  // ======= Painel direito: calendário mensal =======
  let viewMonth = (new Date()).getMonth();
  let viewYear  = (new Date()).getFullYear();

  function refreshCalendar(){
    const cal = document.getElementById('calendar');
    const label = document.getElementById('monthLabel');
    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const first = new Date(viewYear, viewMonth, 1);
    const last  = new Date(viewYear, viewMonth+1, 0);
    label.textContent = `${monthNames[viewMonth]} ${viewYear}`;

    cal.innerHTML='';

    // leading blanks
    for(let i=0;i<first.getDay();i++){
      const empty = document.createElement('div');
      cal.appendChild(empty);
    }

    const today = new Date();

    for(let day=1; day<=last.getDate(); day++){
      const d = new Date(viewYear, viewMonth, day);
      const key = ymd(d);

      const cell = document.createElement('div'); cell.className='cell';
      if(state.checks.has(key)) cell.classList.add('checked');
      if(sameDay(d,today)) cell.classList.add('today');

      const num = document.createElement('div'); num.className='num'; num.textContent=day;
      const dot = document.createElement('div'); dot.className='dot';
      cell.appendChild(num); cell.appendChild(dot);

      cell.addEventListener('click', ()=>{
        toggleCheck(d);
        cell.classList.toggle('checked');
        // também atualiza semana/top widgets
        renderWeekRow();
        renderHabitWeekChecks();
        refreshStats();
      });

      cal.appendChild(cell);
    }
  }

  // ======= Estatísticas (Total, Mensal, Rate, Streak) =======
  function refreshStats(){
    const now = new Date();
    const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
    const monthEnd   = new Date(now.getFullYear(), now.getMonth()+1, 0);
    let total = state.checks.size;
    let monthly = 0;

    // streak (dias consecutivos até hoje)
    let streak = 0;
    const cursor = new Date(now);
    while(state.checks.has(ymd(cursor))){
      streak++;
      cursor.setDate(cursor.getDate()-1);
    }

    state.checks.forEach(key=>{
      const [y,m,d] = key.split('-').map(Number);
      const dt = new Date(y, m-1, d);
      if(dt>=monthStart && dt<=monthEnd) monthly++;
    });

    const daysSoFar = (now.getDate()); // no mês corrente
    const rate = daysSoFar>0 ? Math.round((monthly/daysSoFar)*100) : 0;

    // Atualiza UI
    document.getElementById('monthlyCount').textContent = monthly;
    document.getElementById('totalCount').textContent   = total;
    document.getElementById('monthlyRate').textContent  = rate;
    document.getElementById('streakVal').textContent    = streak;

    document.getElementById('badgeTotal').textContent = `${total} Days`;
    document.getElementById('badgeZero').textContent  = streak===0 ? '0 Day' : `${streak} Day`;
  }

  // Navegação do mês
  document.getElementById('prevMonth').addEventListener('click', ()=>{
    viewMonth--; if(viewMonth<0){viewMonth=11; viewYear--;}
    refreshCalendar();
  });
  document.getElementById('nextMonth').addEventListener('click', ()=>{
    viewMonth++; if(viewMonth>11){viewMonth=0; viewYear++;}
    refreshCalendar();
  });

  // Init
  (function init(){
    document.getElementById('panelTitle').textContent = state.name;
    document.getElementById('habitTitle').textContent = state.name;
    renderWeekRow();
    renderHabitWeekChecks();
    refreshCalendar();
    refreshStats();
  })();
</script>
</body>
</html>
