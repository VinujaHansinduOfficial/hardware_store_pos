document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.auto-hide');
    alerts.forEach(alert => setTimeout(() => alert.remove(), 3000));
});
