document.addEventListener('livewire:init', () => {
    Livewire.on('notify', (message) => {
        if (typeof message !== 'string' || message.trim() === '') {
            return;
        }

        showToast(message);
    });
});

const showToast = (message) => {
    const container = ensureToastContainer();
    const toast = document.createElement('div');

    toast.textContent = message;
    toast.style.background = 'rgba(76, 29, 149, 0.9)';
    toast.style.color = '#fff';
    toast.style.padding = '0.75rem 1.25rem';
    toast.style.borderRadius = '9999px';
    toast.style.fontSize = '0.875rem';
    toast.style.fontWeight = '600';
    toast.style.boxShadow = '0 10px 25px rgba(79, 70, 229, 0.3)';
    toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(12px)';

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(12px)';

        toast.addEventListener('transitionend', () => {
            toast.remove();
        }, { once: true });
    }, 2400);
};

const ensureToastContainer = () => {
    let container = document.getElementById('toast-container');

    if (! container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.position = 'fixed';
        container.style.bottom = '1.5rem';
        container.style.right = '1.5rem';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '0.75rem';
        container.style.zIndex = '9999';

        document.body.appendChild(container);
    }

    return container;
};
