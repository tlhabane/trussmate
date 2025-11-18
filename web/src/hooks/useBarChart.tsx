import React, { useLayoutEffect, useCallback, useRef } from 'react';
import * as am5core from '@amcharts/amcharts5';
import * as am5xy from '@amcharts/amcharts5/xy';
import am5themes_Animated from '@amcharts/amcharts5/themes/Animated';

export const useBarChart = (chartData: any[], chartHeight = '350px', chartWidth = '100%') => {
    const barChartContainer = useRef<HTMLDivElement | null>(null);
    
    const makeSeries = useCallback(
        (
            root: am5core.Root,
            chart: am5xy.XYChart,
            legend: am5core.Legend,
            data: Record<string, string | number | boolean>[],
            xAxis: am5xy.CategoryAxis<am5xy.AxisRenderer>,
            yAxis: am5xy.ValueAxis<am5xy.AxisRenderer>,
            name: string,
            fieldName: string,
            columnFillColors?: string[],
        ) => {
            const series = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name,
                    xAxis,
                    yAxis,
                    valueYField: fieldName,
                    categoryXField: 'invoiceMonth',
                }),
            );
            
            series.columns.template.setAll({
                tooltipText: '{name}, {categoryX}:{valueY}',
                width: am5core.percent(90),
                tooltipY: 0,
                strokeOpacity: 0,
            });
            
            if (columnFillColors && columnFillColors.length > 2) {
                series.columns.template.set(
                    'fillGradient',
                    am5core.LinearGradient.new(root, {
                        stops: [
                            {
                                color: am5core.color(columnFillColors[0]),
                            },
                            {
                                color: am5core.color(columnFillColors[1]),
                            },
                            {
                                color: am5core.color(columnFillColors[2]),
                            },
                        ],
                        rotation: 90,
                    }),
                );
            }
            
            series.data.setAll(chartData);
            
            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            series.appear();
            
            series.bullets.push(() =>
                am5core.Bullet.new(root, {
                    locationY: 0,
                    sprite: am5core.Label.new(root, {
                        text: '{valueY}',
                        fill: root.interfaceColors.get('alternativeText'),
                        centerY: 0,
                        centerX: am5core.p50,
                        populateText: true,
                    }),
                }),
            );
            
            legend.data.push(series);
        },
        [chartData, chartHeight, chartWidth],
    );
    
    useLayoutEffect(() => {
        let root: am5core.Root | null = null;
        if (barChartContainer && barChartContainer.current) {
            root = am5core.Root.new(barChartContainer.current as HTMLElement);
            if (root) {
                root.setThemes([am5themes_Animated.new(root)]);
                
                const chart = root.container.children.push(
                    am5xy.XYChart.new(root, {
                        panX: false,
                        panY: false,
                        wheelX: 'panX',
                        wheelY: 'zoomX',
                        layout: root.verticalLayout,
                    }),
                );
                
                // Legend
                const legend = chart.children.push(
                    am5core.Legend.new(root, {
                        centerX: am5core.p50,
                        x: am5core.p50,
                    }),
                );
                // Axis
                const xRenderer = am5xy.AxisRendererX.new(root, {
                    cellStartLocation: 0.1,
                    cellEndLocation: 0.9,
                });
                
                const xAxis = chart.xAxes.push(
                    am5xy.CategoryAxis.new(root, {
                        categoryField: 'invoiceMonth',
                        renderer: xRenderer,
                        tooltip: am5core.Tooltip.new(root, {}),
                    }),
                );
                
                xRenderer.grid.template.setAll({
                    location: 1,
                });
                
                xAxis.data.setAll(chartData);
                
                const yAxis = chart.yAxes.push(
                    am5xy.ValueAxis.new(root, {
                        renderer: am5xy.AxisRendererY.new(root, {
                            strokeOpacity: 0.1,
                        }),
                    }),
                );
                
                const seriesColumnColors: Record<string, string[]> = {
                    0: ['#ff0000', '#b00606', '#5a1212'],
                    1: ['#5ff621', '#41b006', '#316405'],
                    2: ['#ffaa00', '#ff8600', '#ff7400'],
                    3: ['#008fff', '#0658b0', '#043975'],
                };
                
                makeSeries(
                    root,
                    chart,
                    legend,
                    chartData,
                    xAxis,
                    yAxis,
                    'Sales',
                    'saleTotal',
                    seriesColumnColors[2],
                );
                makeSeries(
                    root,
                    chart,
                    legend,
                    chartData,
                    xAxis,
                    yAxis,
                    'Payments',
                    'paymentTotal',
                    seriesColumnColors[1],
                );
                makeSeries(
                    root,
                    chart,
                    legend,
                    chartData,
                    xAxis,
                    yAxis,
                    'Current Balance',
                    'invoiceBalance',
                    seriesColumnColors[3],
                );
                makeSeries(
                    root,
                    chart,
                    legend,
                    chartData,
                    xAxis,
                    yAxis,
                    'Overdue',
                    'overdueInvoiceBalance',
                    seriesColumnColors[0],
                );
                /*
                makeSeries(root, chart, legend, chartData, xAxis, yAxis, 'Balance', 'balance', seriesColumnColors[3]);
                */
                
                chart.appear(1000, 100).then((r) => {
                    // eslint-disable-next-line no-console
                    console.log(r);
                });
            }
        }
        
        return () => {
            if (root) {
                root.dispose();
            }
        };
    }, [chartData, chartHeight, chartWidth]);
    
    return <div ref={barChartContainer} style={{ width: chartWidth, height: chartHeight }} />;
};
