class NotificationSystem {
    constructor() {
        this.container = null;
        this.queue = [];
        this.activeNotifications = new Set();
        this.maxVisible = 3;
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.createContainer());
        } else {
            this.createContainer();
        }
    }

    createContainer() {
        if (this.container) return;

        this.container = document.getElementById('notification-container');
        if (!this.container) {
            console.warn('Notification container not found in DOM');
        }
    }

    showNotification(message, type = 'info', duration = 5000) {
        if (!this.container) {
            this.createContainer();
            if (!this.container) return;
        }

        const notification = this.createNotificationElement(message, type, duration);
        
        if (this.activeNotifications.size >= this.maxVisible) {
            this.queue.push({ message, type, duration });
            return;
        }

        this.displayNotification(notification, duration);
    }

    createNotificationElement(message, type, duration) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.setAttribute('role', 'alert');
        notification.setAttribute('aria-live', 'polite');

        const icon = this.getIcon(type);
        
        notification.innerHTML = `
            <div class="notification-icon">
                ${icon}
            </div>
            <div class="notification-content">
                <p class="notification-message">${this.escapeHtml(message)}</p>
            </div>
            <button class="notification-close" aria-label="Close notification">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            ${duration > 0 ? '<div class="notification-progress"><div class="notification-progress-bar"></div></div>' : ''}
        `;

        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => this.hideNotification(notification));

        if (duration > 0) {
            let isPaused = false;
            notification.addEventListener('mouseenter', () => {
                isPaused = true;
            });
            notification.addEventListener('mouseleave', () => {
                isPaused = false;
            });
        }

        return notification;
    }

    displayNotification(notification, duration) {
        this.container.appendChild(notification);
        this.activeNotifications.add(notification);

        requestAnimationFrame(() => {
            notification.classList.add('show');
        });

        if (duration > 0) {
            const progressBar = notification.querySelector('.notification-progress-bar');
            if (progressBar) {
                progressBar.style.width = '100%';
                progressBar.style.transition = `width ${duration}ms linear`;
                
                requestAnimationFrame(() => {
                    progressBar.style.width = '0%';
                });
            }

            let startTime = Date.now();
            let remainingTime = duration;
            let timeoutId;

            const scheduleHide = () => {
                timeoutId = setTimeout(() => {
                    this.hideNotification(notification);
                }, remainingTime);
            };

            notification.addEventListener('mouseenter', () => {
                clearTimeout(timeoutId);
                remainingTime -= Date.now() - startTime;
                if (progressBar) {
                    const currentWidth = parseFloat(progressBar.style.width);
                    progressBar.style.transition = 'none';
                    progressBar.style.width = currentWidth + '%';
                }
            });

            notification.addEventListener('mouseleave', () => {
                startTime = Date.now();
                if (progressBar) {
                    progressBar.style.transition = `width ${remainingTime}ms linear`;
                    progressBar.style.width = '0%';
                }
                scheduleHide();
            });

            scheduleHide();
        }
    }

    hideNotification(notification) {
        notification.classList.remove('show');
        notification.classList.add('hide');

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.activeNotifications.delete(notification);

            if (this.queue.length > 0) {
                const next = this.queue.shift();
                this.showNotification(next.message, next.type, next.duration);
            }
        }, 300);
    }

    getIcon(type) {
        const icons = {
            success: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            `,
            error: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            `,
            warning: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            `,
            info: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `
        };
        return icons[type] || icons.info;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    success(message, duration = 5000) {
        this.showNotification(message, 'success', duration);
    }

    error(message, duration = 5000) {
        this.showNotification(message, 'error', duration);
    }

    warning(message, duration = 5000) {
        this.showNotification(message, 'warning', duration);
    }

    info(message, duration = 5000) {
        this.showNotification(message, 'info', duration);
    }

    clearAll() {
        this.activeNotifications.forEach(notification => {
            this.hideNotification(notification);
        });
        this.queue = [];
    }
}

const notifications = new NotificationSystem();

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);

    const success = urlParams.get('success');
    if (success) {
        notifications.success(success);
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, '', url);
    }

    const error = urlParams.get('error');
    if (error) {
        notifications.error(error);
        const url = new URL(window.location);
        url.searchParams.delete('error');
        window.history.replaceState({}, '', url);
    }

    const warning = urlParams.get('warning');
    if (warning) {
        notifications.warning(warning);
        const url = new URL(window.location);
        url.searchParams.delete('warning');
        window.history.replaceState({}, '', url);
    }

    const info = urlParams.get('info');
    if (info) {
        notifications.info(info);
        const url = new URL(window.location);
        url.searchParams.delete('info');
        window.history.replaceState({}, '', url);
    }
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationSystem;
}

