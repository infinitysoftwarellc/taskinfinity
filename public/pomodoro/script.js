/* ======================================================================
   Pomodoro page logic — vanilla JavaScript with localStorage persistence
   ====================================================================== */
(function () {
    'use strict';

    // Wait for the DOM to be ready to safely query elements
    document.addEventListener('DOMContentLoaded', () => {
        const root = document.querySelector('[data-module="pomodoro"]');
        if (!root) {
            return; // Page not present, nothing to do.
        }

        /* --------------------------------------------------------------
           DOM references
           -------------------------------------------------------------- */
        const timeEl = root.querySelector('[data-role="time"]');
        const startBtn = root.querySelector('[data-role="start"]');
        const stopBtn = root.querySelector('[data-role="stop"]');
        const ringEl = root.querySelector('.pomodoro-ring');

        const statTodayPomo = root.querySelector('[data-role="stat-today-pomo"]');
        const statTodayHours = root.querySelector('[data-role="stat-today-hours"]');
        const statTodayMinutes = root.querySelector('[data-role="stat-today-minutes"]');
        const statTotalPomo = root.querySelector('[data-role="stat-total-pomo"]');
        const statTotalHours = root.querySelector('[data-role="stat-total-hours"]');
        const statTotalMinutes = root.querySelector('[data-role="stat-total-minutes"]');
        const recordList = root.querySelector('[data-role="record-list"]');
        const dateLabel = root.querySelector('[data-role="date-label"]');

        /* --------------------------------------------------------------
           Constants and helpers
           -------------------------------------------------------------- */
        const POMO_SECONDS = 20 * 60; // Base mock duration: 20 minutes
        const MAX_RECORDS = 5; // Display and persist only the latest five entries
        const STORAGE_KEY = 'ti-pomodoro-state-v1';
        const MS_IN_SECOND = 1000;

        // Minimal beep encoded as a data URI (pure sine wave burst)
        const completionBeep = new Audio('data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAQAAAAAAAAgICAf39/f4CAgICAgP///w==');
        completionBeep.preload = 'auto';

        const pad = (value) => String(value).padStart(2, '0');
        const formatClock = (seconds) => `${pad(Math.floor(seconds / 60))}:${pad(seconds % 60)}`;
        const formatDateLabel = (date) => date.toLocaleDateString('en-US', { month: 'short', day: '2-digit' });
        const getDayKey = (date) => date.toISOString().slice(0, 10); // YYYY-MM-DD

        const formatTimeLabel = (date) => `${pad(date.getHours())}:${pad(date.getMinutes())}`;
        const safeNumber = (value, fallback = 0) => {
            const parsed = Number(value);
            return Number.isFinite(parsed) ? parsed : fallback;
        };

        // Sample focus records used on the very first visit (mirrors the mockup)
        const defaultRecords = () => {
            const today = new Date();
            // Generate sample items anchored on today to keep ordering predictable
            const baseTimes = [
                { start: { h: 22, m: 23 }, end: { h: 22, m: 39 }, dur: 15 },
                { start: { h: 22, m: 3 }, end: { h: 22, m: 23 }, dur: 20 },
                { start: { h: 21, m: 15 }, end: { h: 21, m: 35 }, dur: 20 },
                { start: { h: 20, m: 54 }, end: { h: 21, m: 14 }, dur: 20 },
                { start: { h: 20, m: 33 }, end: { h: 20, m: 53 }, dur: 20 }
            ];

            return baseTimes.map((slot) => {
                const start = new Date(today);
                start.setHours(slot.start.h, slot.start.m, 0, 0);
                const end = new Date(today);
                end.setHours(slot.end.h, slot.end.m, 0, 0);
                return {
                    startedAt: start.toISOString(),
                    endedAt: end.toISOString(),
                    durationMinutes: slot.dur
                };
            });
        };

        /* --------------------------------------------------------------
           State management with persistence
           -------------------------------------------------------------- */
        const loadState = () => {
            try {
                const raw = window.localStorage.getItem(STORAGE_KEY);
                if (!raw) {
                    // First run → seed with mock data to match the design
                    const initialDate = new Date();
                    return {
                        remaining: POMO_SECONDS,
                        running: false,
                        lastTick: Date.now(),
                        todaysKey: getDayKey(initialDate),
                        todaysPomo: 36,
                        todaysFocusMinutes: 11 * 60 + 34,
                        totalPomo: 1606,
                        totalFocusMinutes: 528 * 60 + 29,
                        records: defaultRecords()
                    };
                }

                const parsed = JSON.parse(raw);
                return parsed;
            } catch (error) {
                console.error('Failed to read Pomodoro state:', error);
                return {
                    remaining: POMO_SECONDS,
                    running: false,
                    lastTick: Date.now(),
                    todaysKey: getDayKey(new Date()),
                    todaysPomo: 0,
                    todaysFocusMinutes: 0,
                    totalPomo: 0,
                    totalFocusMinutes: 0,
                    records: []
                };
            }
        };

        const saveState = () => {
            try {
                state.lastTick = Date.now();
                const snapshot = {
                    remaining: state.remaining,
                    running: state.running,
                    lastTick: state.lastTick,
                    todaysKey: state.todaysKey,
                    todaysPomo: state.todaysPomo,
                    todaysFocusMinutes: state.todaysFocusMinutes,
                    totalPomo: state.totalPomo,
                    totalFocusMinutes: state.totalFocusMinutes,
                    records: state.records.slice(0, MAX_RECORDS)
                };
                window.localStorage.setItem(STORAGE_KEY, JSON.stringify(snapshot));
            } catch (error) {
                console.error('Failed to save Pomodoro state:', error);
            }
        };

        let state = loadState();
        let timerId = null;

        // Reset daily counters if the stored date belongs to a different day
        const today = new Date();
        const todayKey = getDayKey(today);
        if (state.todaysKey !== todayKey) {
            state.todaysKey = todayKey;
            state.todaysPomo = 0;
            state.todaysFocusMinutes = 0;
        }

        // Sanity checks for numeric fields
        state.remaining = Math.max(0, Math.min(POMO_SECONDS, safeNumber(state.remaining, POMO_SECONDS)));
        state.todaysPomo = Math.max(0, Math.round(safeNumber(state.todaysPomo, 0)));
        state.todaysFocusMinutes = Math.max(0, Math.round(safeNumber(state.todaysFocusMinutes, 0)));
        state.totalPomo = Math.max(0, Math.round(safeNumber(state.totalPomo, 0)));
        state.totalFocusMinutes = Math.max(0, Math.round(safeNumber(state.totalFocusMinutes, 0)));
        state.records = Array.isArray(state.records) ? state.records.slice(0, MAX_RECORDS) : [];

        /* --------------------------------------------------------------
           Rendering helpers
           -------------------------------------------------------------- */
        const applyRingProgress = (remainingSeconds) => {
            const progressRatio = Math.max(0, Math.min(1, 1 - remainingSeconds / POMO_SECONDS));
            const degrees = progressRatio * 360;
            ringEl.style.setProperty('--progress', `${degrees}deg`);
        };

        const renderRecords = () => {
            recordList.innerHTML = '';
            state.records.slice(0, MAX_RECORDS).forEach((record) => {
                const startedDate = record.startedAt ? new Date(record.startedAt) : null;
                const endedDate = record.endedAt ? new Date(record.endedAt) : null;
                const startedValid = startedDate && !Number.isNaN(startedDate.getTime());
                const endedValid = endedDate && !Number.isNaN(endedDate.getTime());
                const startedLabel = startedValid ? formatTimeLabel(startedDate) : (record.start ?? record.startLabel ?? '--:--');
                const endedLabel = endedValid ? formatTimeLabel(endedDate) : (record.end ?? record.endLabel ?? '--:--');
                const parsedDuration = Number(record.durationMinutes);
                const fallbackDuration = Number(record.durMin ?? record.duration ?? 0);
                const durationValue = Number.isFinite(parsedDuration) ? parsedDuration : (Number.isFinite(fallbackDuration) ? fallbackDuration : 0);
                const durationLabel = `${Math.max(0, Math.round(durationValue))}m`;

                const item = document.createElement('div');
                item.className = 'pomodoro-timeline__item';
                item.setAttribute('role', 'listitem');
                item.innerHTML = `
                    <div class="pomodoro-timeline__dotbox">
                        <div class="pomodoro-timeline__line" aria-hidden="true"></div>
                        <div class="pomodoro-timeline__dot" aria-hidden="true"></div>
                    </div>
                    <div class="pomodoro-timeline__time">${startedLabel} – ${endedLabel}</div>
                    <div class="pomodoro-timeline__duration">${durationLabel}</div>
                `;
                recordList.appendChild(item);
            });
        };

        const renderStats = () => {
            statTodayPomo.textContent = state.todaysPomo;
            statTodayHours.textContent = Math.floor(state.todaysFocusMinutes / 60);
            statTodayMinutes.textContent = state.todaysFocusMinutes % 60;
            statTotalPomo.textContent = state.totalPomo;
            statTotalHours.textContent = Math.floor(state.totalFocusMinutes / 60);
            statTotalMinutes.textContent = state.totalFocusMinutes % 60;
        };

        const renderTimer = () => {
            timeEl.textContent = formatClock(state.remaining);
            applyRingProgress(state.remaining);
            startBtn.textContent = state.running ? 'Pause' : 'Start';
            startBtn.setAttribute('aria-pressed', state.running ? 'true' : 'false');
        };

        const renderDate = () => {
            dateLabel.textContent = formatDateLabel(new Date());
        };

        const renderAll = () => {
            renderTimer();
            renderStats();
            renderRecords();
            renderDate();
        };

        /* --------------------------------------------------------------
           Timer mechanics
           -------------------------------------------------------------- */
        const handleTick = () => {
            const nowTime = Date.now();
            const elapsed = Math.floor((nowTime - state.lastTick) / MS_IN_SECOND);
            if (elapsed <= 0) {
                return;
            }

            state.remaining = Math.max(0, state.remaining - elapsed);
            state.lastTick = nowTime;

            if (state.remaining <= 0) {
                completePomodoro({ silent: false });
                return;
            }

            renderTimer();
            // Store progress every few seconds to avoid excessive writes
            if (state.remaining % 5 === 0) {
                saveState();
            }
        };

        const startTimer = () => {
            if (timerId) {
                window.clearInterval(timerId);
            }
            state.running = true;
            state.lastTick = Date.now();
            timerId = window.setInterval(handleTick, MS_IN_SECOND);
            renderTimer();
            saveState();
        };

        const pauseTimer = () => {
            if (timerId) {
                window.clearInterval(timerId);
                timerId = null;
            }
            state.running = false;
            saveState();
            renderTimer();
        };

        const hardStop = () => {
            if (timerId) {
                window.clearInterval(timerId);
                timerId = null;
            }
            state.running = false;
            state.remaining = POMO_SECONDS;
            saveState();
            renderAll();
        };

        const addRecord = ({ startedAt, endedAt, durationMinutes }) => {
            state.records.unshift({ startedAt, endedAt, durationMinutes });
            if (state.records.length > MAX_RECORDS) {
                state.records.length = MAX_RECORDS;
            }
        };

        const completePomodoro = ({ silent = false } = {}) => {
            if (timerId) {
                window.clearInterval(timerId);
                timerId = null;
            }

            const completionMoment = new Date();
            const completionKey = getDayKey(completionMoment);
            if (state.todaysKey !== completionKey) {
                state.todaysKey = completionKey;
                state.todaysPomo = 0;
                state.todaysFocusMinutes = 0;
            }

            state.running = false;
            state.remaining = POMO_SECONDS;

            state.todaysPomo += 1;
            state.totalPomo += 1;
            state.todaysFocusMinutes += POMO_SECONDS / 60;
            state.totalFocusMinutes += POMO_SECONDS / 60;

            const endedAt = completionMoment;
            const startedAt = new Date(endedAt.getTime() - POMO_SECONDS * MS_IN_SECOND);
            addRecord({
                startedAt: startedAt.toISOString(),
                endedAt: endedAt.toISOString(),
                durationMinutes: POMO_SECONDS / 60
            });

            saveState();
            renderAll();

            if (!silent) {
                completionBeep.currentTime = 0;
                completionBeep.play().catch(() => {
                    // Playback may fail if the browser blocks autoplay; ignore silently.
                });
            }
        };

        /* --------------------------------------------------------------
           Event wiring
           -------------------------------------------------------------- */
        startBtn.addEventListener('click', () => {
            if (state.running) {
                pauseTimer();
            } else {
                startTimer();
            }
        });

        stopBtn.addEventListener('click', () => {
            hardStop();
        });

        window.addEventListener('keydown', (event) => {
            if (event.code === 'Space') {
                event.preventDefault();
                state.running ? pauseTimer() : startTimer();
            }
            if (event.key && event.key.toLowerCase() === 'r') {
                event.preventDefault();
                hardStop();
            }
        });

        /* --------------------------------------------------------------
           Initial paint and timer restoration
           -------------------------------------------------------------- */
        renderAll();

        if (state.running) {
            const elapsedSinceLastTick = Math.floor((Date.now() - state.lastTick) / MS_IN_SECOND);
            if (elapsedSinceLastTick > 0) {
                state.remaining = Math.max(0, state.remaining - elapsedSinceLastTick);
            }

            if (state.remaining <= 0) {
                completePomodoro({ silent: true });
            } else {
                startTimer();
            }
        }
    });
})();
