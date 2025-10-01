<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Habit Tracker (compact)</title>
<style>
  :root{
    --bg:#1a1a1a;--panel:#1f1f1f;--card:#252525;--muted:#888;--b:#333;--b2:#2a2a2a;
    --blue:#4a9eff;--blue2:#1e3a5f;--green:#4ade80;--orange:#fb923c;--red:#ef4444;--text:#fff;
  }
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden}
  .row{display:flex}.col{display:flex;flex-direction:column}
  .container{height:100vh}.left{flex:1;padding:24px;border-right:1px solid var(--b2);overflow:auto}
  .right{width:420px;background:var(--panel);padding:24px;overflow:auto}
  .header{align-items:center;justify-content:space-between;margin-bottom:24px}
  .title{font-size:20px;font-weight:600}
  .icon-btn{width:32px;height:32px;border:0;background:transparent;color:var(--muted);border-radius:6px;display:grid;place-items:center;cursor:pointer}
  .icon-btn:hover{background:var(--b2);color:#fff}
  .week{gap:8px;margin-bottom:24px}
  .day{flex:1;text-align:center;padding:12px 8px;border-radius:12px;background:var(--card);cursor:pointer;transition:.2s}
  .day:hover{background:#2d2d2d}.day.active{background:var(--blue2)}
  .day .n{font-size:12px;color:var(--muted)} .day .d{font-size:16px;font-weight:600;margin:6px 0}
  .dot{width:28px;height:28px;border-radius:50%;background:var(--blue);margin:0 auto;display:grid;place-items:center;font-weight:700}
  .label{font-size:13px;color:#666;margin:14px 0;display:flex;align-items:center;gap:8px}
  .card{background:var(--card);border:1px solid #333;border-radius:12px;padding:16px 20px;align-items:center;justify-content:space-between;gap:16px;cursor:pointer}
  .card:hover{background:#2a2a2a;border-color:#444}
  .h-info{align-items:center;gap:12px;flex:1}
  .ava{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#a8e063,#56ab2f);font-size:18px}
  .h-title{font-size:15px;font-weight:500;margin-bottom:6px}
  .h-stats{gap:12px;font-size:12px;color:var(--muted)} .i{gap:4px;align-items:center}
  .chk{width:32px;height:32px;border-radius:50%;background:var(--blue);display:grid;place-items:center;font-weight:700;cursor:pointer}
  .r-head{align-items:center;gap:12px;margin-bottom:24px}
  .r-ttl{font-size:16px;font-weight:500}
  .g{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
  .s-card{background:var(--card);border-radius:12px;padding:16px}
  .s-h{align-items:center;gap:8px;margin-bottom:8px;font-size:13px;color:var(--muted)}
  .s-v{font-size:28px;font-weight:600}.s-u{font-size:14px;color:var(--muted);margin-left:4px}
  .cal-sec{margin-top:12px}.cal-h{align-items:center;justify-content:space-between;margin-bottom:12px}
  .cal-ttl{font-size:16px;font-weight:600}
  .grid{display:grid;grid-template-columns:repeat(7,1fr);gap:8px}
  .hdr{font-size:12px;color:#666;text-align:center;padding:8px 4px}
  .dayc{aspect-ratio:1;display:grid;place-items:center;border-radius:8px;font-size:13px;cursor:pointer;position:relative}
  .dayc:hover{background:var(--b2)} .m0{color:#444}
  .checked{background:transparent}.checked::after{content:"";position:absolute;width:32px;height:32px;background:var(--blue);border-radius:50%;z-index:-1}
  .c-green{color:var(--green)} .c-blue{color:var(--blue)} .c-orange{color:var(--orange)} .c-red{color:var(--red)}
  @media(max-width:900px){.container{flex-direction:column}.right{width:100%;border-top:1px solid var(--b);border-left:0}}
</style>
</head>
<body>
  <div class="container row">
    <!-- LEFT -->
    <section class="left col">
      <div class="header row">
        <div class="title">Habit ⌄</div>
        <div class="row" style="gap:12px">
          <button class="icon-btn">⊞</button><button class="icon-btn">+</button><button class="icon-btn">⋯</button>
        </div>
      </div>

      <!-- Semana (JS render) -->
      <div id="week" class="week row"></div>

      <div class="label">🗓 <span id="dateLabel">Oct 1</span> ✕</div>

      <!-- Card hábito -->
      <div class="card row">
        <div class="h-info row">
          <div class="ava">🌟</div>
          <div class="col">
            <div class="h-title">Acredito Que Coisa Maravilhosas vão Acontecer comigo hoje</div>
            <div class="h-stats row">
              <div class="i row"><span>🔥</span><span>12 Days</span></div>
              <div class="i row"><span>🎯</span><span>12 Days</span></div>
            </div>
          </div>
        </div>
        <div class="chk">✓</div>
      </div>
    </section>

    <!-- RIGHT -->
    <aside class="right col">
      <div class="r-head row">
        <div class="ava" style="width:48px;height:48px"></div>
        <div class="r-ttl" style="flex:1">Acredito Que Coisa Maravilhosas vão ...</div>
        <button class="icon-btn">⋯</button>
      </div>

      <div class="g">
        <div class="s-card">
          <div class="s-h row"><span class="c-green">✓</span><span>Monthly check-ins</span></div>
          <div><span class="s-v">1</span><span class="s-u">Day</span></div>
        </div>
        <div class="s-card">
          <div class="s-h row"><span class="c-blue">📊</span><span>Total Check-Ins</span></div>
          <div><span class="s-v">12</span><span class="s-u">Days</span></div>
        </div>
        <div class="s-card">
          <div class="s-h row"><span class="c-orange">%</span><span>Monthly check-in rate</span></div>
          <div><span class="s-v">3</span><span class="s-u">%</span></div>
        </div>
        <div class="s-card">
          <div class="s-h row"><span class="c-red">🔥</span><span>Current Streak</span></div>
          <div><span class="s-v">12</span><span class="s-u">Days</span></div>
        </div>
      </div>

      <!-- Calendário (JS render) -->
      <section class="cal-sec">
        <div class="cal-h row">
          <div id="calTitle" class="cal-ttl">October 2025</div>
          <div>
            <button id="prev" class="icon-btn">‹</button>
            <button id="next" class="icon-btn">›</button>
          </div>
        </div>
        <div class="grid" id="calGrid">
          <!-- headers -->
        </div>
      </section>
    </aside>
  </div>

<script>
/* ===== helpers ===== */
const $  = (s,sc=document)=>sc.querySelector(s);
const $$ = (s,sc=document)=>[...sc.querySelectorAll(s)];
const pad = n=>n.toString().padStart(2,'0');

/* ===== WEEK RENDER (reduce markup) ===== */
const weekData = [
  ['Thu',25],['Fri',26],['Sat',27],['Sun',28],['Mon',29],['Tue',30],['Wed',1,'active']
];
const week = $('#week');
week.innerHTML = weekData.map(([n,d,act])=>`
  <div class="day ${act||''}">
    <div class="n">${n}</div>
    <div class="d">${d}</div>
    <div class="dot">✓</div>
  </div>
`).join('');
$$('.day',week).forEach(el=>el.addEventListener('click',()=>{
  $$('.day',week).forEach(x=>x.classList.remove('active'));
  el.classList.add('active');
  $('#dateLabel').textContent = `${el.children[0].textContent} ${el.children[1].textContent}`;
}));

/* ===== CALENDAR (auto-generate; fewer lines) ===== */
const calGrid = $('#calGrid');
const calTitle = $('#calTitle');
const headers = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
calGrid.innerHTML = headers.map(h=>`<div class="hdr">${h}</div>`).join('');

let y=2025,m=9; // Oct (0-based)
const checkedSet = new Set(['2025-09-28','2025-09-29','2025-09-30','2025-10-01']); // pre-marcados (exemplo)

function renderCalendar(){
  calTitle.textContent = new Date(y,m).toLocaleDateString('en-US',{month:'long',year:'numeric'});
  // limpa dias antigos (mantém cabeçalhos)
  while(calGrid.children.length>7) calGrid.removeChild(calGrid.lastChild);

  const first = new Date(y,m,1), startDow = first.getDay();
  const daysInMonth = new Date(y,m+1,0).getDate();
  const prevDays = new Date(y,m,0).getDate();

  // leading
  for(let i=startDow-1;i>=0;i--){
    const d = prevDays - i; calGrid.insertAdjacentHTML('beforeend',`<div class="dayc m0">${d}</div>`);
  }
  // current
  for(let d=1; d<=daysInMonth; d++){
    const key = `${y}-${pad(m+1)}-${pad(d)}`;
    const checked = checkedSet.has(key) ? 'checked' : '';
    calGrid.insertAdjacentHTML('beforeend',`<div class="dayc ${checked}" data-key="${key}">${d}</div>`);
  }
  // trailing
  const cells = 7 + startDow + daysInMonth; // 7 hdr + body
  for(let i=1; (cells+i)<=49; i++){
    calGrid.insertAdjacentHTML('beforeend',`<div class="dayc m0">${i}</div>`);
  }

  // click toggle
  $$('.dayc[data-key]').forEach(d=>{
    d.onclick = ()=>{ d.classList.toggle('checked');
      if(d.classList.contains('checked')) checkedSet.add(d.dataset.key);
      else checkedSet.delete(d.dataset.key);
    };
  });
}
renderCalendar();

$('#prev').onclick = ()=>{ m--; if(m<0){m=11;y--;} renderCalendar(); };
$('#next').onclick = ()=>{ m++; if(m>11){m=0;y++;}  renderCalendar(); };

/* ===== micro-interactions ===== */
$('.chk').onclick = (e)=>{ e.stopPropagation(); e.currentTarget.animate([{transform:'scale(.9)'},{transform:'scale(1)'}],{duration:150,iterations:1}); };
</script>
</body>
</html>
