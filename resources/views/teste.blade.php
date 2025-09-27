<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Pomodoro</title>
<style>
  :root{
    --bg:#0f1115;
    --panel:#131720;
    --muted:#8791a7;
    --text:#e6e9f0;
    --blue:#4c6fff;
    --blue-2:#2f5dff;
    --ring-track:#2a2f3a;
    --ring-dim:#1a1f2b;
    --stroke:#1f2532;
    --accent:#3b82f6;
    --success:#22c55e;
    --danger:#ef4444;
    --radius:18px;
    --shadow:0 10px 30px rgba(0,0,0,.35);
  }

  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0;
    background:var(--bg);
    color:var(--text);
    font:14px/1.5 system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial;
  }

  /* --- LAYOUT --- */
  .app{
    display:grid;
    grid-template-columns: 260px 1fr 460px;
    gap:24px;
    height:100vh;
    padding:24px;
  }

  aside.sidebar, aside.rightbar, .main{
    background:var(--panel);
    border:1px solid rgba(255,255,255,.06);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
  }

  /* --- SIDEBAR --- */
  .sidebar{
    display:flex;
    flex-direction:column;
    padding:18px;
  }
  .brand{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:700;
    letter-spacing:.2px;
    margin-bottom:16px;
  }
  .brand .dot{
    width:10px;height:10px;border-radius:50%;
    background:var(--blue);
    box-shadow:0 0 10px rgba(76,111,255,.8);
  }
  .menu{
    margin-top:8px;
    display:flex;flex-direction:column;gap:6px;
  }
  .menu .item{
    display:flex;align-items:center;gap:10px;
    padding:10px 12px;border-radius:12px;
    color:#c6cbe0;text-decoration:none;
    border:1px solid transparent;
    transition:.15s ease;
  }
  .menu .item:hover{background:#0f1420;border-color:#1e2535}
  .menu .item.active{
    background:linear-gradient(180deg,#16213a,#101627);
    border-color:#2a3550;color:#fff;
  }
  .item svg{width:18px;height:18px;opacity:.9}

  .sidebar .spacer{flex:1}
  .tiny{
    color:var(--muted);
    font-size:12px;margin-top:8px
  }

  /* --- MAIN --- */
  .main{
    display:grid;
    grid-template-rows:auto 1fr auto;
    padding:0;
  }
  .main-header{
    display:flex;
    align-items:center;
    gap:10px;
    padding:20px 22px 10px 22px;
    border-bottom:1px solid rgba(255,255,255,.06);
  }
  .backlink{
    display:inline-flex;gap:8px;align-items:center;
    color:var(--muted);text-decoration:none;
    font-size:13px;
  }
  .backlink:hover{color:#cfd6ea}
  .dot-blue{
    width:8px;height:8px;border-radius:50%;
    background:var(--blue);
    box-shadow:0 0 0 3px rgba(76,111,255,.15);
  }
  .task-title{
    margin-left:4px;
    font-weight:600;color:#cfd6ea;
  }

  .center{
    display:grid;place-items:center;
    padding:36px 20px 26px 20px;
  }
  .timer-wrap{
    position:relative;width:360px;height:360px;
    display:grid;place-items:center;
  }
  canvas#ring{
    position:absolute;inset:0;
  }
  .time{
    font-size:54px;
    font-weight:700;
    letter-spacing:1px;
  }
  .controls{
    display:flex;gap:12px;justify-content:center;margin-top:28px;
  }
  .btn{
    appearance:none;border:0;cursor:pointer;
    padding:12px 18px;border-radius:14px;
    background:#0f1524;color:#dbe3ff;
    border:1px solid #253051;
    font-weight:600;letter-spacing:.2px;
    transition:.15s ease;
  }
  .btn:hover{transform:translateY(-1px)}
  .btn.primary{background:var(--blue);border-color:#2b49f5;color:#fff}
  .btn.ghost{background:#12182a;color:#cbd5f7}
  .btn.danger{background:#201319;border-color:#3b2228;color:#ffd5d5}

  .bottom{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 18px;border-top:1px solid rgba(255,255,255,.06);
    color:var(--muted);font-size:13px;
  }

  /* --- RIGHT BAR --- */
  .rightbar{
    display:flex;flex-direction:column;
  }
  .panel-head{
    display:flex;align-items:center;gap:10px;
    padding:18px;border-bottom:1px solid rgba(255,255,255,.06);
    font-weight:700;
  }
  .panel-head svg{opacity:.85}
  .calendar{
    position:relative;
    padding:10px 18px 18px 18px;
    flex:1;overflow:hidden;
  }
  .hours{
    position:absolute;inset:0 12px 0 50px;
  }
  .hr{
    position:absolute;left:0;right:0;height:1px;
    background:rgba(255,255,255,.06);
  }
  .hr-label{
    position:absolute;left:-40px;top:-8px;
    font-size:12px;color:var(--muted);
  }
  .session{
    position:absolute;left:0;right:0;height:28px;
    border-radius:10px;display:flex;align-items:center;
    padding:0 10px;gap:8px;
    background:linear-gradient(90deg,var(--blue),var(--blue-2));
    color:#fff;font-weight:600;font-size:12px;
    box-shadow:0 6px 18px rgba(76,111,255,.35);
    border:1px solid rgba(255,255,255,.25);
  }
  .session .dot{width:8px;height:8px;border-radius:50%;background:#ff4d4f}
  .focus-note{
    border-top:1px solid rgba(255,255,255,.06);
    padding:14px 18px;
  }
  .focus-note h4{margin:6px 0 10px 0}
  textarea{
    width:100%;min-height:110px;resize:vertical;
    background:#0f1422;border:1px solid #20263a;
    color:#dbe3ff;border-radius:12px;padding:12px;
    outline:none;
  }
  textarea:focus{border-color:#2f5dff;box-shadow:0 0 0 3px rgba(76,111,255,.2)}

  /* small screens */
  @media (max-width: 1200px){
    .app{grid-template-columns: 220px 1fr 380px}
    .timer-wrap{width:300px;height:300px}
  }
  @media (max-width: 980px){
    .app{grid-template-columns: 1fr}
    .rightbar{order:3}
    .sidebar{order:1}
    .main{order:2}
  }
</style>
</head>
<body>
  <div class="app">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="brand">
        <span class="dot"></span>
        <span>Pomodoro</span>
      </div>

      <nav class="menu">
        <a class="item active" href="#">
          <!-- clock icon -->
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Timer
        </a>
        <a class="item" href="#">
          <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h10M4 17h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
          Tarefas
        </a>
        <a class="item" href="#">
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8Z" stroke="currentColor" stroke-width="1.7"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Histórico
        </a>
        <a class="item" href="#">
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.7"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .66.39 1.26 1 1.51.32.13.66.2 1 .2" stroke="currentColor" stroke-width="1.2"/></svg>
          Configurações
        </a>
      </nav>

      <div class="spacer"></div>
      <div class="tiny">v1.0 • modo escuro</div>
    </aside>

    <!-- MAIN -->
    <section class="main">
      <header class="main-header">
        <a class="backlink" href="#" title="voltar">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span>um novo layout para a tasks</span>
        </a>
        <span class="dot-blue"></span>
        <span class="task-title">› Sessão atual</span>
      </header>

      <div class="center">
        <div class="timer-wrap">
          <canvas id="ring" width="360" height="360"></canvas>
          <div class="time" id="time">25:00</div>
        </div>

        <div class="controls">
          <button class="btn primary" id="btnStart">Start</button>
          <button class="btn" id="btnPause">Pause</button>
          <button class="btn ghost" id="btnResume" style="display:none">Resume</button>
          <button class="btn danger" id="btnReset">Reset</button>
        </div>
      </div>

      <div class="bottom">
        <div>Duração: <strong id="labelDur">25 min</strong></div>
        <div>Estado: <strong id="labelState">parado</strong></div>
      </div>
    </section>

    <!-- RIGHT BAR -->
    <aside class="rightbar">
      <div class="panel-head">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M7 11h10M7 7h10M7 15h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        <span>um novo layout para a tasks</span>
      </div>

      <div class="calendar" id="calendar">
        <div class="hours" id="hours"></div>
      </div>

      <div class="focus-note">
        <h4>Focus Note</h4>
        <textarea placeholder="What do you have in mind?" id="note"></textarea>
      </div>
    </aside>
  </div>

<script>
  // ====== TIMER + RING ======
  const ring = document.getElementById('ring');
  const ctx = ring.getContext('2d');
  const R = 150;              // raio do arco
  const THICK = 14;           // espessura do arco
  const CENTER = { x: ring.width/2, y: ring.height/2 };

  const timeEl = document.getElementById('time');
  const btnStart = document.getElementById('btnStart');
  const btnPause = document.getElementById('btnPause');
  const btnResume = document.getElementById('btnResume');
  const btnReset = document.getElementById('btnReset');
  const labelState = document.getElementById('labelState');
  const labelDur = document.getElementById('labelDur');

  let totalSeconds = 25*60;     // duração configurada (padrão 25 min)
  let remaining = totalSeconds; // segundos restantes
  let timer = null;
  let startedAt = null;         // Date do início (para pintar na agenda)

  // Desenha trilha + progresso
  function drawRing(progress){
    ctx.clearRect(0,0,ring.width, ring.height);

    // trilha externa
    ctx.beginPath();
    ctx.arc(CENTER.x, CENTER.y, R, 0, Math.PI*2);
    ctx.strokeStyle = getComputedStyle(document.documentElement)
      .getPropertyValue('--ring-track').trim();
    ctx.lineWidth = THICK;
    ctx.lineCap = 'round';
    ctx.stroke();

    // arco "pré-passado" (leve)
    ctx.beginPath();
    ctx.arc(CENTER.x, CENTER.y, R, -Math.PI/2, -Math.PI/2 + Math.PI*2);
    ctx.strokeStyle = getComputedStyle(document.documentElement)
      .getPropertyValue('--ring-dim').trim();
    ctx.lineWidth = THICK;
    ctx.stroke();

    // progresso
    const end = -Math.PI/2 + (Math.PI*2)*progress; // 0..1
    const grad = ctx.createLinearGradient(0,0,ring.width, ring.height);
    grad.addColorStop(0,'#4c6fff'); grad.addColorStop(1,'#2f5dff');

    ctx.beginPath();
    ctx.arc(CENTER.x, CENTER.y, R, -Math.PI/2, end);
    ctx.strokeStyle = grad;
    ctx.lineWidth = THICK+2;
    ctx.shadowBlur = 8;
    ctx.shadowColor = 'rgba(76,111,255,.6)';
    ctx.stroke();
    ctx.shadowBlur = 0;
  }

  function formatTime(sec){
    const m = Math.floor(sec/60).toString().padStart(2,'0');
    const s = Math.floor(sec%60).toString().padStart(2,'0');
    return `${m}:${s}`;
  }

  function updateView(){
    const progress = 1 - (remaining / totalSeconds);
    drawRing(progress);
    timeEl.textContent = formatTime(remaining);
  }

  function tick(){
    if (remaining <= 0){
      clearInterval(timer); timer=null;
      labelState.textContent = 'finalizado';
      notifyEnd();
      return;
    }
    remaining -= 1;
    updateView();
    updateSessionBlock(); // move o bloco na agenda em tempo real
  }

  function start(){
    if (timer) return;
    startedAt = new Date();
    remaining = totalSeconds;
    updateView();
    timer = setInterval(tick, 1000);
    labelState.textContent = 'rodando';
    btnStart.style.display='none';
    btnPause.style.display='';
    btnResume.style.display='none';
    addSessionBlock(); // cria bloco na agenda
  }

  function pause(){
    if (!timer) return;
    clearInterval(timer); timer=null;
    labelState.textContent = 'pausado';
    btnPause.style.display='none';
    btnResume.style.display='';
  }

  function resume(){
    if (timer) return;
    timer = setInterval(tick,1000);
    labelState.textContent = 'rodando';
    btnPause.style.display='';
    btnResume.style.display='none';
  }

  function reset(){
    clearInterval(timer); timer=null;
    remaining = totalSeconds;
    labelState.textContent = 'parado';
    btnStart.style.display='';
    btnPause.style.display='';
    btnResume.style.display='none';
    updateView();
    removeSessionBlock();
  }

  function notifyEnd(){
    // vibração / beep simples
    try{ navigator.vibrate && navigator.vibrate([100,80,100]); }catch(e){}
    alert('Sessão concluída!');
  }

  btnStart.addEventListener('click', start);
  btnPause.addEventListener('click', pause);
  btnResume.addEventListener('click', resume);
  btnReset.addEventListener('click', reset);

  // Inicial
  labelDur.textContent = (totalSeconds/60) + ' min';
  updateView();

  // ====== RIGHT BAR: AGENDA (11h–15h) ======
  const hoursWrap = document.getElementById('hours');
  const CAL_START = 11; // 11:00
  const CAL_END   = 15; // 15:00
  const calendar  = document.getElementById('calendar');
  const hourHeight = () => calendar.clientHeight / (CAL_END - CAL_START);

  function renderGrid(){
    hoursWrap.innerHTML = '';
    const hH = hourHeight();
    for (let h=CAL_START; h<=CAL_END; h++){
      const line = document.createElement('div');
      line.className='hr';
      line.style.top = ((h-CAL_START)*hH) + 'px';
      const label = document.createElement('div');
      label.className='hr-label';
      label.textContent = h.toString().padStart(2,'0');
      line.appendChild(label);
      hoursWrap.appendChild(line);
    }
  }

  // bloco da sessão
  let sessionDiv = null;

  function minutesFromDay(d){
    return d.getHours()*60 + d.getMinutes();
  }
  function yForMinutes(min){
    const minStart = CAL_START*60;
    const minEnd   = CAL_END*60;
    const rel = (min - minStart) / (minEnd - minStart);
    return Math.max(0, Math.min(1, rel)) * (calendar.clientHeight);
  }

  function addSessionBlock(){
    if (sessionDiv) return;
    const startMin = minutesFromDay(startedAt);
    const endMin = startMin + Math.round(totalSeconds/60);

    sessionDiv = document.createElement('div');
    sessionDiv.className='session';
    sessionDiv.innerHTML = `<span class="dot"></span><span id="slotLabel">${minsToLabel(startMin)}-${minsToLabel(endMin)}</span>`;
    hoursWrap.appendChild(sessionDiv);

    // posiciona
    updateSessionBlock();
  }

  function updateSessionBlock(){
    if (!sessionDiv || !startedAt) return;
    const startMin = minutesFromDay(startedAt);
    const elapsed = totalSeconds - remaining;
    const endMin = startMin + Math.round((elapsed + Math.max(remaining,0))/60);

    const hH = hourHeight();
    const y  = yForMinutes(startMin);
    const y2 = yForMinutes(endMin);
    sessionDiv.style.top = (Math.min(y, calendar.clientHeight-28)) + 'px';
    sessionDiv.style.height = Math.max(14, (y2 - y)) + 'px';

    const label = sessionDiv.querySelector('#slotLabel');
    label.textContent = `${minsToLabel(startMin)}-${minsToLabel(startMin + Math.round(totalSeconds/60))}`;
  }

  function removeSessionBlock(){
    if (sessionDiv && sessionDiv.parentNode){
      sessionDiv.parentNode.removeChild(sessionDiv);
    }
    sessionDiv = null;
  }

  function minsToLabel(mins){
    const h = Math.floor(mins/60), m = mins%60;
    return `${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}`;
  }

  window.addEventListener('resize', ()=>{
    renderGrid();
    updateSessionBlock();
  });

  renderGrid();

  // ====== EXTRAS ======
  // Atalho: tecla espaço pausa/continua
  window.addEventListener('keydown', (e)=>{
    if (e.code === 'Space'){
      e.preventDefault();
      if (!timer && remaining === totalSeconds) start();
      else if (timer) pause(); else resume();
    }
  });

  // Inicia automaticamente uma sessão de demonstração de 20 min como no print?
  // Se quiser 20:00 por padrão, descomente abaixo:
  // totalSeconds = 20*60; remaining = totalSeconds; labelDur.textContent = '20 min'; updateView();

</script>
</body>
</html>
