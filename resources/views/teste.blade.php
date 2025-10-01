<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pomodoro Timer</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    :root{
      --bg:#1a1a1a;--panel:#242424;--border:#333;--muted:#8a8a8a;--text:#fff;--primary:#4a69ff;--primary-2:#5c77ff;--danger:#ff4d4d;--chip:#2a2a2a
    }
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex}
    .container{display:flex;width:100%;max-width:1400px;margin:0 auto}

    /* LEFT */
    .left-panel{flex:1;padding:40px;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative}
    .header{position:absolute;top:30px;left:30px;display:flex;align-items:center;gap:10px}
    .header h1{font-size:20px;font-weight:600}

    .action-buttons{position:absolute;top:30px;right:30px;display:flex;gap:15px}
    .action-btn{width:35px;height:35px;background:transparent;border:1px solid var(--border);border-radius:8px;color:#999;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s}
    .action-btn:hover{border-color:#555;color:#bbb}

    .focus-label{position:absolute;top:96px;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:5px;color:#777;font-size:14px}

    .timer-container{width:350px;height:350px;border-radius:50%;border:8px solid var(--chip);display:flex;align-items:center;justify-content:center;position:relative;margin-top:50px}
    .timer-display{font-size:72px;font-weight:300;letter-spacing:2px}

    /* Controls now BELOW the ring */
    .controls{display:flex;gap:12px;margin-top:28px}
    .btn{border:0;border-radius:28px;padding:12px 28px;font-size:16px;font-weight:600;cursor:pointer;transition:.18s}
    .btn.primary{background:var(--primary);color:#fff}
    .btn.primary:hover{background:var(--primary-2);transform:translateY(-1px)}
    .btn.secondary{background:#2b2b2b;color:#ddd;border:1px solid var(--border)}
    .btn.secondary:hover{background:#333}
    .btn.stop{background:var(--danger);color:#fff}
    .btn.stop:hover{filter:brightness(1.05)}

    /* RIGHT */
    .right-panel{width:400px;background:var(--panel);padding:30px;border-left:1px solid var(--border)}
    .overview-section{margin-bottom:36px}
    .section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
    .section-title{font-size:18px;font-weight:600}

    .stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .stat-card{background:#1f1f1f;border:1px solid var(--border);border-radius:14px;padding:14px 16px;display:flex;flex-direction:column;gap:6px}
    .stat-label{font-size:12px;color:#8b8b8b}
    .stat-value{font-size:28px;font-weight:700}
    .stat-value small{font-size:16px;font-weight:500;color:#b5b5b5}

    .focus-record{margin-top:20px}
    .date-label{font-size:14px;color:#8b8b8b;margin:10px 0 12px}
    .record-list{display:flex;flex-direction:column}
    .record-item{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #2a2a2a}
    .record-item:last-child{border-bottom:none}
    .record-left{display:flex;align-items:center;gap:12px}
    .record-icon{width:8px;height:8px;background:var(--primary);border-radius:50%}
    .record-time{font-size:14px;color:#cfcfcf}
    .record-duration{font-size:14px;color:#9b9b9b}
    .more-btn{background:transparent;border:none;color:#999;cursor:pointer;font-size:20px;padding:5px}
    .more-btn:hover{color:#bbb}

    @media (max-width:768px){
      .container{flex-direction:column}
      .right-panel{width:100%;border-left:none;border-top:1px solid var(--border)}
      .timer-container{width:280px;height:280px}
      .timer-display{font-size:56px}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <div class="header"><h1>Pomodoro</h1></div>
      <div class="action-buttons">
        <button class="action-btn" title="Adicionar">+</button>
        <button class="action-btn" title="Mais">⋯</button>
      </div>
      <div class="focus-label">Focus</div>

      <div class="timer-container">
        <div class="timer-display" id="timerDisplay">20:00</div>
      </div>

      <!-- CONTROLES: Start / Pause, Stop (reset total) -->
      <div class="controls">
        <button class="btn primary" id="startBtn">Start</button>
        <button class="btn stop" id="stopBtn">Stop</button>
      </div>
    </div>

    <div class="right-panel">
      <div class="overview-section">
        <div class="section-header">
          <h2 class="section-title">Overview</h2>
        </div>

        <div class="stats-grid">
          <div class="stat-card">
            <span class="stat-label">Today's Pomo</span>
            <div class="stat-value" id="todaysPomo">36</div>
          </div>
          <div class="stat-card">
            <span class="stat-label">Today's Focus</span>
            <div class="stat-value" id="todaysFocus">11<small>h</small>34<small>m</small></div>
          </div>
          <div class="stat-card">
            <span class="stat-label">Total Pomo</span>
            <div class="stat-value">1606</div>
          </div>
          <div class="stat-card">
            <span class="stat-label">Total Focus Duration</span>
            <div class="stat-value">528<small>h</small>29<small>m</small></div>
          </div>
        </div>
      </div>

      <div class="focus-record">
        <div class="section-header">
          <h2 class="section-title">Focus Record</h2>
          <div style="display:flex;gap:10px">
            <button class="action-btn">+</button>
            <button class="more-btn">⋯</button>
          </div>
        </div>

        <div class="date-label">Sep 30</div>
        <div class="record-list" id="recordList">
          <div class="record-item"><div class="record-left"><div class="record-icon"></div><span class="record-time">22:23 - 22:39</span></div><span class="record-duration">15m</span></div>
          <div class="record-item"><div class="record-left"><div class="record-icon"></div><span class="record-time">22:03 - 22:23</span></div><span class="record-duration">20m</span></div>
          <div class="record-item"><div class="record-left"><div class="record-icon"></div><span class="record-time">21:15 - 21:35</span></div><span class="record-duration">20m</span></div>
          <div class="record-item"><div class="record-left"><div class="record-icon"></div><span class="record-time">20:54 - 21:14</span></div><span class="record-duration">20m</span></div>
          <div class="record-item"><div class="record-left"><div class="record-icon"></div><span class="record-time">20:33 - 20:53</span></div><span class="record-duration">20m</span></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Estado do timer
    let timeLeft = 20*60; // 20:00
    let isRunning = false;
    let timerInterval = null;
    let pomodoroCount = 36;
    let todayFocusMinutes = 11*60 + 34; // 11h34m

    const timerDisplay = document.getElementById('timerDisplay');
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const recordList = document.getElementById('recordList');

    const todaysPomoEl = document.getElementById('todaysPomo');
    const todaysFocusEl = document.getElementById('todaysFocus');

    function fmt(mm){
      const m = Math.floor(mm/60).toString().padStart(2,'0');
      const s = (mm%60).toString().padStart(2,'0');
      return `${m}:${s}`;
    }
    function updateDisplay(){ timerDisplay.textContent = fmt(timeLeft); }

    function updateStats(){
      todaysPomoEl.textContent = pomodoroCount;
      const h = Math.floor(todayFocusMinutes/60); const m = todayFocusMinutes%60;
      todaysFocusEl.innerHTML = `${h}<small>h</small>${m}<small>m</small>`;
    }

    function tick(){
      if(timeLeft<=0){ completePomodoro(); return; }
      timeLeft--; updateDisplay();
    }

    function startTimer(){
      if(isRunning){ // pause
        clearInterval(timerInterval); isRunning=false; startBtn.textContent='Start'; startBtn.classList.remove('secondary'); startBtn.classList.add('primary');
        return;
      }
      isRunning=true; startBtn.textContent='Pause'; startBtn.classList.remove('primary'); startBtn.classList.add('secondary');
      timerInterval = setInterval(tick,1000);
    }

    function stopTimer(){
      clearInterval(timerInterval); isRunning=false; timeLeft = 20*60; updateDisplay();
      startBtn.textContent='Start'; startBtn.classList.remove('secondary'); startBtn.classList.add('primary');
    }

    function completePomodoro(){
      stopTimer();
      pomodoroCount++; todayFocusMinutes += 20; updateStats();
      const now = new Date(); const start = new Date(now.getTime()-20*60*1000);
      const hh = (d)=>d.getHours().toString().padStart(2,'0');
      const mm = (d)=>d.getMinutes().toString().padStart(2,'0');
      const item = document.createElement('div');
      item.className='record-item';
      item.innerHTML = `<div class="record-left"><div class="record-icon"></div><span class="record-time">${hh(start)} - ${hh(now)}:${mm(now)}</span></div><span class="record-duration">20m</span>`;
      recordList.insertBefore(item, recordList.firstChild);
      if(recordList.children.length>5){ recordList.removeChild(recordList.lastChild); }
    }

    startBtn.addEventListener('click', startTimer);
    stopBtn.addEventListener('click', stopTimer);

    document.addEventListener('keydown', (e)=>{ if(e.code==='Space'){ e.preventDefault(); startTimer(); } });

    updateDisplay();
    updateStats();
  </script>
</body>
</html>