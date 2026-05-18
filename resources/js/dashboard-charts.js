const moneyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});

const readTheme = () => {
    const styles = getComputedStyle(document.body);

    return {
        primary: styles.getPropertyValue('--sx-primary').trim() || '#ff2a2a',
        primaryStrong: styles.getPropertyValue('--sx-primary-strong').trim() || '#d70f0f',
        text: styles.getPropertyValue('--sx-text').trim() || '#ffffff',
        muted: styles.getPropertyValue('--sx-muted').trim() || '#b5b5b5',
        border: styles.getPropertyValue('--sx-border').trim() || 'rgba(255,255,255,.08)',
        glow: styles.getPropertyValue('--sx-primary-glow').trim() || 'rgba(255,42,42,.25)',
        success: styles.getPropertyValue('--sx-success').trim() || '#34d399',
        danger: styles.getPropertyValue('--sx-danger').trim() || '#f87171',
        surface: styles.getPropertyValue('--sx-surface').trim() || '#111111',
    };
};

const baseOptions = (theme) => ({
    chart: {
        background: 'transparent',
        foreColor: theme.muted,
        toolbar: { show: false },
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 650,
        },
        fontFamily: 'Figtree, ui-sans-serif, system-ui, sans-serif',
    },
    grid: {
        borderColor: theme.border,
        strokeDashArray: 4,
        padding: {
            left: 12,
            right: 12,
        },
    },
    tooltip: {
        theme: 'dark',
        style: {
            fontSize: '12px',
            fontFamily: 'Figtree, ui-sans-serif, system-ui, sans-serif',
        },
        y: {
            formatter: (value) => moneyFormatter.format(value),
        },
    },
    dataLabels: { enabled: false },
    legend: {
        labels: {
            colors: theme.muted,
        },
    },
    xaxis: {
        labels: {
            style: {
                colors: theme.muted,
            },
        },
        axisBorder: {
            color: theme.border,
        },
        axisTicks: {
            color: theme.border,
        },
    },
    yaxis: {
        labels: {
            style: {
                colors: theme.muted,
            },
            formatter: (value) => moneyFormatter.format(value),
        },
    },
});

const mountChart = (ApexCharts, selector, options) => {
    const element = document.querySelector(selector);

    if (!element) {
        return null;
    }

    element.innerHTML = '';

    const chart = new ApexCharts(element, options);
    chart.render();

    return chart;
};

const initDashboardCharts = async () => {
    const root = document.getElementById('dashboard-charts-data');

    if (!root) {
        return;
    }

    const { default: ApexCharts } = await import('apexcharts');

    const data = JSON.parse(root.textContent || '{}');
    const theme = readTheme();
    const shared = baseOptions(theme);
    const labelsDias = data.labelsDias || [];

    mountChart(ApexCharts, '#chart-fluxo-financeiro', {
        ...shared,
        chart: {
            ...shared.chart,
            type: 'area',
            height: 330,
            dropShadow: {
                enabled: true,
                top: 10,
                blur: 18,
                opacity: 0.16,
                color: theme.primary,
            },
        },
        series: [
            { name: 'Entradas', data: data.entradasPorDia || [] },
            { name: 'Saídas', data: data.saidasPorDia || [] },
        ],
        colors: [theme.success, theme.danger],
        stroke: {
            curve: 'smooth',
            width: 3,
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.34,
                opacityTo: 0.04,
                stops: [0, 90, 100],
            },
        },
        xaxis: {
            ...shared.xaxis,
            categories: labelsDias,
            tickAmount: 6,
        },
    });

    mountChart(ApexCharts, '#chart-categorias', {
        ...shared,
        chart: {
            ...shared.chart,
            type: 'donut',
            height: 330,
        },
        series: data.valoresPorCategoria?.length ? data.valoresPorCategoria : [1],
        labels: data.categoriasSaida?.length ? data.categoriasSaida : ['Sem saídas'],
        colors: [theme.primary, theme.primaryStrong, theme.danger, theme.success, '#60a5fa', '#a78bfa'],
        stroke: {
            colors: [theme.surface],
            width: 4,
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        name: {
                            color: theme.muted,
                        },
                        value: {
                            color: theme.text,
                            formatter: (value) => moneyFormatter.format(Number(value)),
                        },
                        total: {
                            show: true,
                            label: 'Saídas',
                            color: theme.muted,
                            formatter: (chart) => moneyFormatter.format(
                                chart.globals.seriesTotals.reduce((total, value) => total + value, 0),
                            ),
                        },
                    },
                },
            },
        },
        tooltip: {
            ...shared.tooltip,
            y: {
                formatter: (value) => moneyFormatter.format(value),
            },
        },
        legend: {
            ...shared.legend,
            position: 'bottom',
        },
    });

    mountChart(ApexCharts, '#chart-saldo-acumulado', {
        ...shared,
        chart: {
            ...shared.chart,
            type: 'area',
            height: 330,
            dropShadow: {
                enabled: true,
                top: 10,
                blur: 20,
                opacity: 0.18,
                color: theme.primary,
            },
        },
        series: [
            { name: 'Saldo acumulado', data: data.saldoAcumuladoPorDia || [] },
        ],
        colors: [theme.primary],
        stroke: {
            curve: 'smooth',
            width: 3,
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.8,
                opacityFrom: 0.42,
                opacityTo: 0.05,
                stops: [0, 90, 100],
            },
        },
        xaxis: {
            ...shared.xaxis,
            categories: labelsDias,
            tickAmount: 6,
        },
    });

    const margin = Number(data.margemPercentual || 0);
    const radialValue = Math.max(0, Math.min(100, margin));

    mountChart(ApexCharts, '#chart-saude-financeira', {
        ...shared,
        chart: {
            ...shared.chart,
            type: 'radialBar',
            height: 330,
        },
        series: [radialValue],
        colors: [margin >= 0 ? theme.primary : theme.danger],
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                hollow: {
                    size: '66%',
                    background: 'transparent',
                },
                track: {
                    background: theme.border,
                    strokeWidth: '98%',
                },
                dataLabels: {
                    name: {
                        show: true,
                        offsetY: 22,
                        color: theme.muted,
                        fontSize: '13px',
                        fontWeight: 800,
                    },
                    value: {
                        show: true,
                        offsetY: -16,
                        color: theme.text,
                        fontSize: '34px',
                        fontWeight: 900,
                        formatter: () => `${margin.toLocaleString('pt-BR')}%`,
                    },
                },
            },
        },
        labels: ['Margem'],
        stroke: {
            lineCap: 'round',
        },
        tooltip: {
            enabled: false,
        },
    });
};

document.addEventListener('DOMContentLoaded', initDashboardCharts);
