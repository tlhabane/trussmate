import React, { useRef, useLayoutEffect } from 'react';
import * as am5core from '@amcharts/amcharts5';
import * as am5radar from '@amcharts/amcharts5/radar';
import * as am5xy from '@amcharts/amcharts5/xy';
import am5themes_Animated from '@amcharts/amcharts5/themes/Animated';

type ChartData = {
    title: string;
    total: number;
};

export const useRadarChart = (chartData: ChartData[], chartHeight = '350px', chartWidth = '100%') => {
    const solidGaugeChartContainer = useRef<HTMLDivElement | null>(null);
    
    useLayoutEffect(() => {
        let root: am5core.Root | null = null;
        
        if (solidGaugeChartContainer && solidGaugeChartContainer.current) {
            root = am5core.Root.new(solidGaugeChartContainer.current as HTMLElement);
            root.setThemes([am5themes_Animated.new(root)]);
            
            const chart = root.container.children.push(
                am5radar.RadarChart.new(root, {
                    panX: false,
                    panY: false,
                    wheelX: 'panX',
                    wheelY: 'zoomX',
                    innerRadius: am5core.percent(20),
                    startAngle: -90,
                    endAngle: 180,
                }),
            );
            
            const seriesColumnColors: Record<string, string[]> = {
                0: ['#41b006', '#5ff621'],
                1: ['#d67409', '#ffaa00'],
                2: ['#ff0000', '#b00606'],
            };
            
            const updatedChartData = chartData.map((item, index) => ({
                category: item.title,
                value: item.total,
                full: 100,
                columnSettings: {
                    fill: am5core.color(seriesColumnColors[`${index}`][0]),
                    fillGradient: am5core.LinearGradient.new(root as am5core.Root, {
                        stops: [
                            {
                                color: am5core.color(seriesColumnColors[`${index}`][0]),
                            },
                            {
                                color: am5core.color(seriesColumnColors[`${index}`][1]),
                            },
                        ],
                    }),
                },
            }));
            /*
            const data = [
                {
                    category: 'Pending',
                    value: 9,
                    full: 100,
                    columnSettings: {
                        fill: am5core.color(seriesColumnColors[0][0]),
                        fillGradient: am5core.LinearGradient.new(root, {
                            stops: [
                                {
                                    color: am5core.color(seriesColumnColors[0][0]),
                                },
                                {
                                    color: am5core.color(seriesColumnColors[0][1]),
                                },
                            ],
                        }),
                    },
                },
                {
                    category: 'In-Progress',
                    value: 23,
                    full: 100,
                    columnSettings: {
                        fill: am5core.color(seriesColumnColors[1][0]),
                        fillGradient: am5core.LinearGradient.new(root, {
                            stops: [
                                {
                                    color: am5core.color(seriesColumnColors[1][0]),
                                },
                                {
                                    color: am5core.color(seriesColumnColors[1][1]),
                                },
                            ],
                        }),
                    },
                },
                {
                    category: 'Completed',
                    value: 68,
                    full: 100,
                    columnSettings: {
                        fill: am5core.color(seriesColumnColors[2][0]),
                        fillGradient: am5core.LinearGradient.new(root, {
                            stops: [
                                {
                                    color: am5core.color(seriesColumnColors[2][0]),
                                },
                                {
                                    color: am5core.color(seriesColumnColors[2][1]),
                                },
                            ],
                        }),
                    },
                },
            ];
            */
            
            const cursor = chart.set(
                'cursor',
                am5radar.RadarCursor.new(root, {
                    behavior: 'zoomX',
                }),
            );
            
            cursor.lineY.set('visible', false);
            
            // Axis
            const xRenderer = am5radar.AxisRendererCircular.new(root, {
                // minGridDistance: 50
            });
            
            xRenderer.labels.template.setAll({
                radius: 10,
            });
            
            xRenderer.grid.template.setAll({
                forceHidden: true,
            });
            
            const xAxis = chart.xAxes.push(
                am5xy.ValueAxis.new(root, {
                    renderer: xRenderer,
                    min: 0,
                    max: 100,
                    strictMinMax: true,
                    numberFormat: '#\'%\'',
                    tooltip: am5core.Tooltip.new(root, {}),
                }),
            );
            
            const yRenderer = am5radar.AxisRendererRadial.new(root, {
                minGridDistance: 20,
            });
            
            yRenderer.labels.template.setAll({
                centerX: am5core.p100,
                fontWeight: '500',
                fontSize: 18,
                templateField: 'columnSettings',
            });
            
            yRenderer.grid.template.setAll({
                forceHidden: true,
            });
            
            const yAxis = chart.yAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: 'category',
                    renderer: yRenderer,
                }),
            );
            
            yAxis.data.setAll(updatedChartData);
            
            // Series
            const series1 = chart.series.push(
                am5radar.RadarColumnSeries.new(root, {
                    xAxis,
                    yAxis,
                    clustered: false,
                    valueXField: 'full',
                    categoryYField: 'category',
                    fill: root.interfaceColors.get('alternativeBackground'),
                }),
            );
            
            series1.columns.template.setAll({
                width: am5core.p100,
                fillOpacity: 0.08,
                strokeOpacity: 0,
                cornerRadius: 20,
            });
            
            series1.data.setAll(updatedChartData);
            
            const series2 = chart.series.push(
                am5radar.RadarColumnSeries.new(root, {
                    xAxis,
                    yAxis,
                    clustered: false,
                    valueXField: 'value',
                    categoryYField: 'category',
                }),
            );
            
            series2.columns.template.setAll({
                width: am5core.p100,
                strokeOpacity: 0,
                tooltipText: '{category}: {valueX}%',
                cornerRadius: 20,
                templateField: 'columnSettings',
            });
            
            series2.data.setAll(updatedChartData);
            
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100).then((r) => {
                // eslint-disable-next-line no-console
                console.log(r);
            });
        }
        
        return () => {
            if (root) {
                root.dispose();
            }
        };
    }, [chartData, chartHeight, chartWidth]);
    
    return <div ref={solidGaugeChartContainer} style={{ width: chartWidth, height: chartHeight }} />;
};
