/* ----------------------------------------------------------------------
   Pomodoro Livewire enhancements (audio feedback, Livewire hooks)
   ---------------------------------------------------------------------- */

const completionBeep = new Audio(
  'data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAQAAAAAAAAgICAf39/f4CAgICAgP///w=='
);
completionBeep.preload = 'auto';

if (typeof document !== 'undefined') {
  document.addEventListener('livewire:init', () => {
    if (typeof window.Livewire === 'undefined') {
      return;
    }

    window.Livewire.on('pomodoro-completed', () => {
      try {
        completionBeep.currentTime = 0;
        void completionBeep.play();
      } catch (_error) {
        // Audio playback may fail if the browser blocks autoplay; ignore silently.
      }
    });
  });
}
