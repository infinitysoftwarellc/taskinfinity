<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pomodoro Timer</title>
  <style>
    :root{
      --bg: #0f1115;
      --panel: #12161e;
      --panel-2: #0d1117;
      --card: #141a23;
      --border: #1b2230;
      --text: #e6e8ee;
      --muted: #91a0b6;
      --brand: #3b82f6;
      --brand-2: #5b8cff;
      --ring: #2a3344;
      --success: #22c55e;
      --radius: 16px;
      --shadow: 0 10px 30px rgba(0,0,0,.35);
    }
    
    *, *::before, *::after { box-sizing: border-box; }
    
    html, body { 
      height: 100%; 
      margin: 0;
      padding: 0;
    }
    
    body {
      background: var(--bg);
      color: var(--text);
      font: 14px/1.45 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      overflow-x: hidden;
    }

    /* APP LAYOUT */
    .app {
      display: grid;
      grid-template-columns: minmax(600px, 1fr) 400px;
      gap: 20px;
      min-height: 100vh;
      padding: 24px;
      max-width: 1400px;
      margin: 0 auto;
    }

    .left {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .right {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    /* TOPBAR */
    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 8px;
    }

    .title {
      font-weight: 700;
      font-size: 20px;
      letter-spacing: 0.2px;
    }

    .header-actions {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .icon-btn {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: var(--panel);
      display: grid;
      place-items: center;
      border: 1px solid var(--border);
      color: var(--muted);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .icon-btn:hover {
      background: var(--card);
      color: var(--text);
    }

    /* MODE TABS */
    .modes {
      display: flex;
      gap: 4px;
      background: var(--panel);
      border: 1px solid var(--border);
      padding: 4px;
      border-radius: 999px;
      margin-right: 12px;
    }

    .mode {
      padding: 8px 14px;
      border-radius: 999px;
      color: var(--muted);
      background: transparent;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
      font-size: 13px;
      font-weight: 500;
    }

    .mode.active {
      background: linear-gradient(180deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05));
      color: #cfe1ff;
    }

    /* BREADCRUMB */
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--muted);
      font-size: 13px;
      margin-bottom: 4px;
    }

    .breadcrumb .chevron {
      opacity: 0.6;
      font-size: 12px;
    }

    /* TIME CONTROLS */
    .time-controls {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 8px 0 20px 0;
    }

    .time-label {
      color: var(--muted);
      font-size: 13px;
      font-weight: 500;
    }

    .stepper {
      display: flex;
      align-items: center;
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      background: var(--panel);
    }

    .stepper button {
      width: 36px;
      height: 36px;
      background: transparent;
      border: 0;
      color: var(--text);
      cursor: pointer;
      display: grid;
      place-items: center;
      transition: background 0.2s ease;
    }

    .stepper button:hover {
      background: var(--card);
    }

    .stepper input {
      width: 64px;
      text-align: center;
      background: var(--panel-2);
      color: var(--text);
      border: 0;
      height: 36px;
      font-size: 13px;
      font-weight: 500;
    }

    .stepper input:focus {
      outline: none;
      background: var(--card);
    }

    .preset-chip {
      padding: 8px 12px;
      border-radius: 999px;
      background: var(--panel);
      border: 1px solid var(--border);
      color: var(--text);
      cursor: pointer;
      transition: all 0.2s ease;
      font-size: 12px;
      font-weight: 500;
    }

    .preset-chip:hover {
      background: var(--card);
    }

    .preset-chip.active {
      background: var(--brand);
      border-color: transparent;
      color: white;
      box-shadow: 0 6px 16px rgba(59,130,246,0.35);
    }

    /* TIMER CARD */
    .timer-card {
      flex: 1;
      background: var(--panel-2);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 40px 24px;
      box-shadow: var(--shadow);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 32px;
      min-height: 500px;
    }

    /* TIMER DIAL */
    .dial {
      --size: 280px;
      --progress: 0;
      position: relative;
      width: var(--size);
      height: var(--size);
      border-radius: 50%;
      display: grid;
      place-items: center;
      background: 
        radial-gradient(closest-side, var(--panel-2) 82%, transparent 0),
        conic-gradient(var(--brand) calc(var(--progress) * 1%), #263246 0);
      transition: all 0.3s ease;
    }

    .dial::after {
      content: "";
      position: absolute;
      inset: 12px;
      border-radius: 50%;
      background: var(--panel-2);
      border: 1px solid var(--border);
    }

    .time-display {
      position: relative;
      font-size: 42px;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--text);
      z-index: 2;
    }

    /* CONTROLS */
    .controls {
      display: flex;
      gap: 12px;
    }

    .btn {
      min-width: 110px;
      padding: 12px 20px;
      border-radius: 999px;
      border: 1px solid var(--border);
      background: var(--panel);
      color: var(--text);
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn:hover {
      background: var(--card);
    }

    .btn.primary {
      background: var(--brand);
      border-color: transparent;
      color: white;
      box-shadow: 0 6px 18px rgba(59,130,246,0.35);
    }

    .btn.primary:hover {
      background: var(--brand-2);
      transform: translateY(-1px);
    }

    /* SIDEBAR */
    .sidebar-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .sidebar-title {
      font-size: 16px;
      font-weight: 600;
      margin: 0;
    }

    .sidebar-actions {
      display: flex;
      gap: 6px;
    }

    /* OVERVIEW CARDS */
    .overview {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 20px;
    }

    .metric-card {
      background: var(--panel-2);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 16px;
      box-shadow: var(--shadow);
    }

    .metric-label {
      margin: 0 0 8px 0;
      color: var(--muted);
      font-weight: 600;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .metric-value {
      font-size: 24px;
      font-weight: 800;
      color: var(--text);
    }

    .metric-value small {
      font-size: 13px;
      color: var(--muted);
      font-weight: 500;
      margin-left: 2px;
    }

    /* FOCUS RECORD */
    .focus-record {
      background: var(--panel-2);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 18px;
      box-shadow: var(--shadow);
    }

    .record-date {
      color: var(--muted);
      font-size: 12px;
      margin: 8px 0 16px 0;
      font-weight: 500;
    }

    .timeline {
      position: relative;
      padding-left: 28px;
    }

    .timeline::before {
      content: "";
      position: absolute;
      left: 12px;
      top: 0;
      bottom: 0;
      width: 2px;
      background: linear-gradient(180deg, #243045, #1a2234);
      border-radius: 1px;
    }

    .timeline-item {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 0;
      margin-bottom: 4px;
    }

    .timeline-dot {
      position: absolute;
      left: -16px;
      top: 50%;
      transform: translateY(-50%);
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, #7aa2ff, #2f4cbb);
      box-shadow: 0 4px 8px rgba(59,130,246,0.25);
    }

    .timeline-time {
      color: var(--text);
      font-size: 13px;
      font-weight: 500;
    }

    .timeline-duration {
      color: var(--muted);
      font-size: 13px;
      font-weight: 500;
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
      .app {
        grid-template-columns: 1fr;
        padding: 16px;
      }
      
      .right {
        order: -1;
      }
      
      .overview {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 768px) {
      .app {
        padding: 12px;
        gap: 16px;
      }
      
      .overview {
        grid-template-columns: 1fr 1fr;
      }
      
      .dial {
        --size: 240px;
      }
      
      .time-display {
        font-size: 36px;
      }
      
      .timer-card {
        min-height: 400px;
        padding: 32px 20px;
      }
    }

    /* ANIMATIONS */
    .timer-complete {
      animation: pulse 1.4s ease-in-out;
    }

    @keyframes pulse {
      0%, 100% { 
        filter: none;
        transform: scale(1);
      }
      50% { 
        filter: drop-shadow(0 0 20px rgba(59,130,246,0.6));
        transform: scale(1.02);
      }
    }

    .running .dial {
      box-shadow: 0 0 30px rgba(59,130,246,0.2);
    }
  </style>
</head>
<body>
  <div class="app">
    <div class="left">
      <div class="topbar">
        <div class="title">Pomodoro</div>
        <div class="header-actions">
          <div class="modes">
            <button class="mode active" data-mode="pomo">Pomo</button>
            <button class="mode" data-mode="stopwatch">Stopwatch</button>
          </div>
          <button class="icon-btn" title="Add">+</button>
          <button class="icon-btn" title="More">⋯</button>
        </div>
      </div>

      <div class="breadcrumb">
        <span>Focus</span> 
        <span class="chevron">›</span>
      </div>

      <div class="time-controls">
        <span class="time-label">Pomodoro:</span>
        <div class="stepper">
          <button id="decrease" type="button">−</button>
          <input id="minutes-input" type="number" min="1" max="120" value="25" />
          <button id="increase" type="button">+</button>
        </div>
        <button class="preset-chip active" data-minutes="25">25m</button>
        <button class="preset-chip" data-minutes="5">5m</button>
        <button class="preset-chip" data-minutes="15">15m</button>
      </div>

      <div class="timer-card" id="timer-container">
        <div class="dial" id="timer-dial">
          <div class="time-display" id="time-display">25:00</div>
        </div>
        <div class="controls">
          <button class="btn primary" id="start-btn">Start</button>
          <button class="btn" id="reset-btn">Reset</button>
        </div>
      </div>
    </div>

    <aside class="right">
      <div class="sidebar-header">
        <h4 class="sidebar-title">Overview</h4>
        <div class="sidebar-actions">
          <button class="icon-btn" title="Add">+</button>
          <button class="icon-btn" title="More">⋯</button>
        </div>
      </div>

      <div class="overview">
        <div class="metric-card">
          <h5 class="metric-label">Today's Pomo</h5>
          <div class="metric-value" id="today-pomo">0</div>
        </div>
        <div class="metric-card">
          <h5 class="metric-label">Today's Focus</h5>
          <div class="metric-value" id="today-focus">0 <small>m</small></div>
        </div>
        <div class="metric-card">
          <h5 class="metric-label">Total Pomo</h5>
          <div class="metric-value">1496</div>
        </div>
        <div class="metric-card">
          <h5 class="metric-label">Total Focus Duration</h5>
          <div class="metric-value">493<small>h</small> 23<small>m</small></div>
        </div>
      </div>

      <div class="focus-record">
        <div class="sidebar-header">
          <h4 class="sidebar-title">Focus Record</h4>
          <div class="sidebar-actions">
            <button class="icon-btn" title="Add">+</button>
            <button class="icon-btn" title="More">⋯</button>
          </div>
        </div>
        
        <div class="record-date" id="current-date">Sep 28</div>
        <div class="timeline" id="timeline">
          <!-- Timeline items will be added here -->
        </div>
        
        <div class="record-date">Sep 27</div>
        <div class="timeline">
          <div class="timeline-item">
            <span class="timeline-dot"></span>
            <span class="timeline-time">19:38 – 19:55</span>
            <span class="timeline-duration">17m</span>
          </div>
          <div class="timeline-item">
            <span class="timeline-dot"></span>
            <span class="timeline-time">19:17 – 19:37</span>
            <span class="timeline-duration">20m</span>
          </div>
          <div class="timeline-item">
            <span class="timeline-dot"></span>
            <span class="timeline-time">18:57 – 19:17</span>
            <span class="timeline-duration">20m</span>
          </div>
          <div class="timeline-item">
            <span class="timeline-dot"></span>
            <span class="timeline-time">18:36 – 18:56</span>
            <span class="timeline-duration">20m</span>
          </div>
          <div class="timeline-item">
            <span class="timeline-dot"></span>
            <span class="timeline-time">18:15 – 18:35</span>
            <span class="timeline-duration">20m</span>
          </div>
        </div>
      </div>
    </aside>
  </div>

  <script>
    class PomodoroTimer {
      constructor() {
        this.totalSeconds = 25 * 60;
        this.remainingSeconds = this.totalSeconds;
        this.isRunning = false;
        this.interval = null;
        this.startTime = null;
        this.sessionHistory = [];
        this.todayPomos = 0;
        this.todayFocus = 0;
        
        this.initElements();
        this.bindEvents();
        this.render();
        this.updateDate();
        this.loadTodayStats();
      }

      initElements() {
        this.timeDisplay = document.getElementById('time-display');
        this.timerDial = document.getElementById('timer-dial');
        this.startBtn = document.getElementById('start-btn');
        this.resetBtn = document.getElementById('reset-btn');
        this.minutesInput = document.getElementById('minutes-input');
        this.decreaseBtn = document.getElementById('decrease');
        this.increaseBtn = document.getElementById('increase');
        this.presetChips = document.querySelectorAll('.preset-chip');
        this.timerContainer = document.getElementById('timer-container');
        this.todayPomoEl = document.getElementById('today-pomo');
        this.todayFocusEl = document.getElementById('today-focus');
        this.timeline = document.getElementById('timeline');
      }

      bindEvents() {
        this.startBtn.addEventListener('click', () => this.toggleTimer());
        this.resetBtn.addEventListener('click', () => this.resetTimer());
        this.decreaseBtn.addEventListener('click', () => this.adjustMinutes(-1));
        this.increaseBtn.addEventListener('click', () => this.adjustMinutes(1));
        this.minutesInput.addEventListener('change', () => this.setMinutes(parseInt(this.minutesInput.value)));
        
        this.presetChips.forEach(chip => {
          chip.addEventListener('click', () => {
            const minutes = parseInt(chip.dataset.minutes);
            this.setMinutes(minutes);
          });
        });
      }

      formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
      }

      render() {
        this.timeDisplay.textContent = this.formatTime(this.remainingSeconds);
        const progress = ((this.totalSeconds - this.remainingSeconds) / this.totalSeconds) * 100;
        this.timerDial.style.setProperty('--progress', progress);
      }

      toggleTimer() {
        if (this.isRunning) {
          this.pauseTimer();
        } else {
          this.startTimer();
        }
      }

      startTimer() {
        this.isRunning = true;
        this.startTime = Date.now();
        this.startBtn.textContent = 'Pause';
        this.timerContainer.classList.add('running');
        
        this.interval = setInterval(() => {
          this.remainingSeconds = Math.max(0, this.remainingSeconds - 1);
          this.render();
          
          if (this.remainingSeconds === 0) {
            this.completeSession();
          }
        }, 1000);
      }

      pauseTimer() {
        this.isRunning = false;
        this.startBtn.textContent = 'Start';
        this.timerContainer.classList.remove('running');
        clearInterval(this.interval);
      }

      resetTimer() {
        this.pauseTimer();
        this.remainingSeconds = this.totalSeconds;
        this.render();
      }

      completeSession() {
        this.pauseTimer();
        this.timerContainer.classList.add('timer-complete');
        
        // Record session
        const duration = Math.floor(this.totalSeconds / 60);
        this.recordSession(duration);
        this.updateTodayStats(1, duration);
        
        setTimeout(() => {
          this.timerContainer.classList.remove('timer-complete');
          this.resetTimer();
        }, 1400);
      }

      adjustMinutes(delta) {
        const currentMinutes = parseInt(this.minutesInput.value);
        const newMinutes = Math.max(1, Math.min(120, currentMinutes + delta));
        this.setMinutes(newMinutes);
      }

      setMinutes(minutes) {
        minutes = Math.max(1, Math.min(120, minutes));
        this.minutesInput.value = minutes;
        this.totalSeconds = minutes * 60;
        
        if (!this.isRunning) {
          this.remainingSeconds = this.totalSeconds;
          this.render();
        }
        
        this.updatePresetChips(minutes);
      }

      updatePresetChips(activeMinutes) {
        this.presetChips.forEach(chip => {
          const chipMinutes = parseInt(chip.dataset.minutes);
          chip.classList.toggle('active', chipMinutes === activeMinutes);
        });
      }

      recordSession(duration) {
        const now = new Date();
        const endTime = now;
        const startTime = new Date(endTime.getTime() - (duration * 60 * 1000));
        
        const session = {
          start: startTime.toTimeString().slice(0, 5),
          end: endTime.toTimeString().slice(0, 5),
          duration: duration
        };
        
        this.addToTimeline(session);
      }

      addToTimeline(session) {
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item';
        timelineItem.innerHTML = `
          <span class="timeline-dot"></span>
          <span class="timeline-time">${session.start} – ${session.end}</span>
          <span class="timeline-duration">${session.duration}m</span>
        `;
        
        this.timeline.insertBefore(timelineItem, this.timeline.firstChild);
      }

      updateTodayStats(pomos, focusMinutes) {
        this.todayPomos += pomos;
        this.todayFocus += focusMinutes;
        
        this.todayPomoEl.textContent = this.todayPomos;
        this.todayFocusEl.innerHTML = `${this.todayFocus} <small>m</small>`;
      }

      loadTodayStats() {
        // In a real app, this would load from storage
        this.todayPomos = 1;
        this.todayFocus = 6;
        this.todayPomoEl.textContent = this.todayPomos;
        this.todayFocusEl.innerHTML = `${this.todayFocus} <small>m</small>`;
      }

      updateDate() {
        const today = new Date();
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
          'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const dateStr = `${monthNames[today.getMonth()]} ${today.getDate()}`;
        document.getElementById('current-date').textContent = dateStr;
      }
    }

    // Initialize the timer when the page loads
    document.addEventListener('DOMContentLoaded', () => {
      new PomodoroTimer();
    });
  </script>
</body>
</html>