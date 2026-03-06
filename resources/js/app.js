import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Live clock component
Alpine.data('liveClock', () => ({
    time: '',
    date: '',
    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
    },
    updateClock() {
        const now = new Date();
        this.time = now.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        this.date = now.toLocaleDateString('en-IN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }
}));

// Sidebar toggle for mobile
Alpine.data('sidebar', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    }
}));

// OTP Input handler
Alpine.data('otpInput', () => ({
    digits: ['', '', '', '', '', ''],
    fullOtp: '',
    handleInput(index, event) {
        const value = event.target.value.replace(/[^0-9]/g, '');
        this.digits[index] = value.slice(-1);
        event.target.value = this.digits[index];

        if (value && index < 5) {
            event.target.nextElementSibling?.focus();
        }

        this.fullOtp = this.digits.join('');
    },
    handleKeydown(index, event) {
        if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
            event.target.previousElementSibling?.focus();
        }
    },
    handlePaste(event) {
        event.preventDefault();
        const paste = event.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
        paste.split('').forEach((char, i) => {
            this.digits[i] = char;
        });
        this.fullOtp = this.digits.join('');
        // Focus last filled input
        const inputs = event.target.parentElement.querySelectorAll('input');
        const focusIndex = Math.min(paste.length, 5);
        inputs[focusIndex]?.focus();
    }
}));

// Auto-dismiss alerts
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

Alpine.start();
