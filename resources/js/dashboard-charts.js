const moneyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});

let dashboardChartsStarted = false;

const readTheme = () => {
    const styles = getComputedStyle(document.body);

    return {
        primary: styles.getPropertyValue('--sx-primary').trim() || '#ff2a2a',
        primaryStrong: styles.getPropertyValue('--sx-primary-strong').trim() || '#d70f0f',
        text: styles.getPropertyValue('--sx-text').trim() || '#ffffff',
        muted: styles.getPropertyValue('--sx-muted').trim() || '#b5b5b5',
        border: styles.getPropertyValue('--sx-border').trim() || 'rgba(255,255,255,.08)',
        success: styles.getPropertyValue('--sx-success').trim() || '#34d399',
        danger: styles.getPropertyValue('--sx-danger').trim() || '#f87171',
        surface: styles.getPropertyValue('--sx-surface').trim() || '#111111',
    };
};

const arrayValue = (value) => (Array.isArray(value) ? value : []);

const numericArray = (value) => arrayValue(value).map((item) => {
    const number = Number(item);

    return Number.isFinite(number) ? number : 0;
});

const normalizeSeries = (value, labels) => {
    const series = numericArray(value);
    const size = Math.max(labels.length, series.length);

    return Array.from({ length: size }, (_, index) => series[index] ?? 0);
};

const safeMargin = (value) => {
    const margin = Number(value ?? 0);

    return Number.isFinite(margin) ? margin : 0;
};

const parseDashboardData = () => {
    const root = document.getElementById('dashboard-charts-data');

    if (!root) {
        return null;
    }

    try {
        return JSON.parse(root.textContent || '{}') || {};
    } catch (error) {
        console.warn('[Systex] Dashboard chart data is invalid.', error);

        return {};
    }
};

const chartElement = (...selectors) => {
    for (const selector of selectors) {
        const element = document.querySelector(selector);

        if (element) {
            return element;
        }
    }

    return null;
};

const mountChart = (ApexCharts, element, options) => {
    if (!element) {
        return;
    }

    try {
        element.innerHTML = '';
        const chart = new ApexCharts(element, options);
        Promise.resolve(chart.render()).catch((error) => {
            console.warn('[Systex] Chart render promise failed.', error);
        });
    } catch (error) {
        console.warn('[Systex] Chart render failed.', error);
    }
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
    noData: {
        text: 'Sem dados no período',
        align: 'center',
        verticalAlign: 'middle',
        style: {
            color: theme.muted,
            fontSize: '13px',
            fontFamily: 'Figtree, ui-sans-serif, system-ui, sans-serif',
        },
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
            formatter: (value) => moneyFormatter.format(Number(value) || 0),
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
            formatter: (value) => moneyFormatter.format(Number(value) || 0),
        },
    },
});

const initDashboardCharts = async () => {
    if (dashboardChartsStarted) {
        return;
    }

    const data = parseDashboardData();

    if (!data) {
        return;
    }

    dashboardChartsStarted = true;

    let ApexCharts;

    try {
        ({ default: ApexCharts } = await import('apexcharts'));
    } catch (error) {
        console.warn('[Systex] ApexCharts could not be loaded.', error);

        return;
    }

    const theme = readTheme();
    const shared = baseOptions(theme);
    const labelsDias = arrayValue(data.labelsDias);
    const entradasPorDia = normalizeSeries(data.entradasPorDia, labelsDias);
    const saidasPorDia = normalizeSeries(data.saidasPorDia, labelsDias);
    const saldoAcumulado = normalizeSeries(data.saldoAcumulado ?? data.saldoAcumuladoPorDia, labelsDias);
    const categoriasSaida = arrayValue(data.saidasPorCategoria ?? data.categoriasSaida);
    const valoresPorCategoria = numericArray(data.valoresPorCategoria);
    const margemFinanceira = safeMargin(data.margemFinanceira ?? data.margemPercentual);

    mountChart(ApexCharts, chartElement('#cashflowChart', '#chart-fluxo-financeiro'), {
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
            { name: 'Entradas', data: entradasPorDia },
            { name: 'Saídas', data: saidasPorDia },
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
            tickAmount: Math.min(6, Math.max(labelsDias.length, 1)),
        },
    });

    mountChart(ApexCharts, chartElement('#categoryChart', '#chart-categorias'), {
        ...shared,
        chart: {
            ...shared.chart,
            type: 'donut',
            height: 330,
        },
        series: valoresPorCategoria.length ? valoresPorCategoria : [0],
        labels: categoriasSaida.length ? categoriasSaida : ['Sem saídas'],
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
                            formatter: (value) => moneyFormatter.format(Number(value) || 0),
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
        legend: {
            ...shared.legend,
            position: 'bottom',
        },
    });

    mountChart(ApexCharts, chartElement('#balanceChart', '#chart-saldo-acumulado'), {
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
            { name: 'Saldo acumulado', data: saldoAcumulado },
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
            tickAmount: Math.min(6, Math.max(labelsDias.length, 1)),
        },
    });

    const radialValue = Math.max(0, Math.min(100, margemFinanceira));

    mountChart(ApexCharts, chartElement('#healthGauge', '#chart-saude-financeira'), {
        ...shared,
        chart: {
            ...shared.chart,
            type: 'radialBar',
            height: 330,
        },
        series: [radialValue],
        colors: [margemFinanceira >= 0 ? theme.primary : theme.danger],
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
                        formatter: () => `${margemFinanceira.toLocaleString('pt-BR')}%`,
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

const runWhenReady = () => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardCharts, { once: true });

        return;
    }

    initDashboardCharts();
};

runWhenReady();
