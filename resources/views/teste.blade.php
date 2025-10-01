<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pomodoro Timer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #1a1a1a;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Left Panel */
        .left-panel {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .header {
            position: absolute;
            top: 30px;
            left: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 500;
        }

        .tabs {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 30px;
        }

        .tab {
            padding: 8px 16px;
            background: transparent;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 14px;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .tab.active {
            background: #4a69ff;
            color: white;
        }

        .tab:hover:not(.active) {
            color: #999;
        }

        .focus-label {
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 14px;
        }

        .focus-label::after {
            content: '›';
            margin-left: 3px;
        }

        .timer-container {
            width: 350px;
            height: 350px;
            border-radius: 50%;
            border: 8px solid #2a2a2a;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-top: 50px;
        }

        .timer-display {
            font-size: 72px;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .start-button {
            position: absolute;
            bottom: 120px;
            padding: 12px 50px;
            background: #4a69ff;
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .start-button:hover {
            background: #5c77ff;
            transform: scale(1.05);
        }

        .start-button:active {
            transform: scale(0.98);
        }

        .action-buttons {
            position: absolute;
            top: 30px;
            right: 30px;
            display: flex;
            gap: 15px;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            background: transparent;
            border: 1px solid #333;
            border-radius: 8px;
            color: #666;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .action-btn:hover {
            border-color: #555;
            color: #999;
        }

        /* Right Panel */
        .right-panel {
            width: 400px;
            background: #242424;
            padding: 30px;
            border-left: 1px solid #333;
        }

        .overview-section {
            margin-bottom: 50px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 500;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 500;
        }

        .stat-value small {
            font-size: 18px;
            font-weight: 400;
            color: #999;
        }

        .focus-record {
            margin-top: 40px;
        }

        .date-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .record-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .record-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #2a2a2a;
        }

        .record-item:last-child {
            border-bottom: none;
        }

        .record-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .record-icon {
            width: 8px;
            height: 8px;
            background: #4a69ff;
            border-radius: 50%;
        }

        .record-time {
            font-size: 14px;
            color: #999;
        }

        .record-duration {
            font-size: 14px;
            color: #666;
        }

        .more-btn {
            background: transparent;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 20px;
            padding: 5px;
        }

        .more-btn:hover {
            color: #999;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .right-panel {
                width: 100%;
                border-left: none;
                border-top: 1px solid #333;
            }

            .timer-container {
                width: 280px;
                height: 280px;
            }

            .timer-display {
                font-size: 56px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="header">
                <h1>Pomodoro</h1>
            </div>
            
            <div class="tabs">
                <button class="tab active">Pomo</button>
                <button class="tab">Stopwatch</button>
            </div>

            <div class="action-buttons">
                <button class="action-btn">+</button>
                <button class="action-btn">⋯</button>
            </div>

            <div class="focus-label">Focus</div>

            <div class="timer-container">
                <div class="timer-display" id="timerDisplay">20:00</div>
            </div>

            <button class="start-button" id="startBtn">Start</button>
        </div>

        <div class="right-panel">
            <div class="overview-section">
                <div class="section-header">
                    <h2 class="section-title">Overview</h2>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-label">Today's Pomo</span>
                        <div class="stat-value">36</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Today's Focus</span>
                        <div class="stat-value">11<small>h</small>34<small>m</small></div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Pomo</span>
                        <div class="stat-value">1606</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Focus Duration</span>
                        <div class="stat-value">528<small>h</small>29<small>m</small></div>
                    </div>
                </div>
            </div>

            <div class="focus-record">
                <div class="section-header">
                    <h2 class="section-title">Focus Record</h2>
                    <div style="display: flex; gap: 10px;">
                        <button class="action-btn">+</button>
                        <button class="more-btn">⋯</button>
                    </div>
                </div>

                <div class="date-label">Sep 30</div>

                <div class="record-list">
                    <div class="record-item">
                        <div class="record-left">
                            <div class="record-icon"></div>
                            <span class="record-time">22:23 - 22:39</span>
                        </div>
                        <span class="record-duration">15m</span>
                    </div>
                    <div class="record-item">
                        <div class="record-left">
                            <div class="record-icon"></div>
                            <span class="record-time">22:03 - 22:23</span>
                        </div>
                        <span class="record-duration">20m</span>
                    </div>
                    <div class="record-item">
                        <div class="record-left">
                            <div class="record-icon"></div>
                            <span class="record-time">21:15 - 21:35</span>
                        </div>
                        <span class="record-duration">20m</span>
                    </div>
                    <div class="record-item">
                        <div class="record-left">
                            <div class="record-icon"></div>
                            <span class="record-time">20:54 - 21:14</span>
                        </div>
                        <span class="record-duration">20m</span>
                    </div>
                    <div class="record-item">
                        <div class="record-left">
                            <div class="record-icon"></div>
                            <span class="record-time">20:33 - 20:53</span>
                        </div>
                        <span class="record-duration">20m</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Timer functionality
        let timeLeft = 20 * 60; // 20 minutes in seconds
        let isRunning = false;
        let timerInterval;
        let pomodoroCount = 36;
        let todayFocusMinutes = 694; // 11h 34m in minutes

        const timerDisplay = document.getElementById('timerDisplay');
        const startBtn = document.getElementById('startBtn');

        function updateDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        function startTimer() {
            if (isRunning) {
                pauseTimer();
            } else {
                isRunning = true;
                startBtn.textContent = 'Pause';
                startBtn.style.background = '#ff4444';
                
                timerInterval = setInterval(() => {
                    if (timeLeft > 0) {
                        timeLeft--;
                        updateDisplay();
                    } else {
                        completePomodoro();
                    }
                }, 1000);
            }
        }

        function pauseTimer() {
            isRunning = false;
            startBtn.textContent = 'Start';
            startBtn.style.background = '#4a69ff';
            clearInterval(timerInterval);
        }

        function completePomodoro() {
            pauseTimer();
            pomodoroCount++;
            todayFocusMinutes += 20;
            updateStats();
            addFocusRecord();
            timeLeft = 20 * 60; // Reset timer
            updateDisplay();
            
            // Play notification sound (optional)
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLYijYIG2m98OScTgwOUaltIphCUo7N67NgGwU7k9nuyHMkBl+z0+BILANAsOv7nEELCVep0qMNF0+z6/E0bB3+8PDw8PDw8PDw8PDw8PDw8PDw8PDw8PDw8PDw8PDw8PDw8ODw');
            audio.play().catch(() => {}); // Ignore errors if audio can't play
        }

        function updateStats() {
            // Update today's pomo count
            document.querySelector('.stat-value').textContent = pomodoroCount;
            
            // Update today's focus time
            const hours = Math.floor(todayFocusMinutes / 60);
            const minutes = todayFocusMinutes % 60;
            document.querySelectorAll('.stat-value')[1].innerHTML = `${hours}<small>h</small>${minutes}<small>m</small>`;
        }

        function addFocusRecord() {
            const now = new Date();
            const startTime = new Date(now.getTime() - 20 * 60 * 1000);
            const startStr = `${startTime.getHours().toString().padStart(2, '0')}:${startTime.getMinutes().toString().padStart(2, '0')}`;
            const endStr = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
            
            const recordList = document.querySelector('.record-list');
            const newRecord = document.createElement('div');
            newRecord.className = 'record-item';
            newRecord.innerHTML = `
                <div class="record-left">
                    <div class="record-icon"></div>
                    <span class="record-time">${startStr} - ${endStr}</span>
                </div>
                <span class="record-duration">20m</span>
            `;
            recordList.insertBefore(newRecord, recordList.firstChild);
            
            // Remove last item if more than 5 records
            if (recordList.children.length > 5) {
                recordList.removeChild(recordList.lastChild);
            }
        }

        // Event listeners
        startBtn.addEventListener('click', startTimer);

        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                if (this.textContent === 'Stopwatch') {
                    // Switch to stopwatch mode
                    timeLeft = 0;
                    updateDisplay();
                    document.querySelector('.focus-label').style.display = 'none';
                } else {
                    // Switch to Pomodoro mode
                    timeLeft = 20 * 60;
                    updateDisplay();
                    document.querySelector('.focus-label').style.display = 'flex';
                }
                
                if (isRunning) {
                    pauseTimer();
                }
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                startTimer();
            }
        });

        // Initialize display
        updateDisplay();
    </script>
</body>
</html>