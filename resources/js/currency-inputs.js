const formatBrlCents = (digits) => {
    const cents = Number.parseInt(digits || '0', 10);

    return (cents / 100).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const applyBrlCurrencyMask = (input) => {
    const digits = input.value.replace(/\D/g, '');
    input.value = formatBrlCents(digits);
};

document.addEventListener('input', (event) => {
    if (! event.target.matches('[data-currency-brl]')) {
        return;
    }

    applyBrlCurrencyMask(event.target);
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-currency-brl]').forEach((input) => {
        if (input.value) {
            applyBrlCurrencyMask(input);
        }
    });
});
