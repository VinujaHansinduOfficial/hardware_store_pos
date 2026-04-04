document.addEventListener('DOMContentLoaded', () => {
    const qtyInputs = document.querySelectorAll('.cart-qty');
    qtyInputs.forEach(input => {
        input.addEventListener('input', () => {
            const row = input.closest('tr');
            const price = parseFloat(row.dataset.price || '0');
            const qty = parseFloat(input.value || '0');
            row.querySelector('.line-total').textContent = (price * qty).toFixed(2);
        });
    });
});
