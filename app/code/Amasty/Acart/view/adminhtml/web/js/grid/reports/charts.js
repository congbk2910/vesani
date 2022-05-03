define([
    'uiComponent',
    'mage/translate',
    'Amasty_Acart/js/action/chart',
    'Amasty_Acart/vendor/amcharts/core.min',
    'Amasty_Acart/vendor/amcharts/charts.min',
    'Amasty_Acart/vendor/amcharts/themes/animated.min'
], function (Component, $t, chart) {
    'use strict';

    return Component.extend({
        defaults: {
            reports: {
                rate: 'rate',
                revenue: 'revenue',
                efficiency: 'efficiency'
            },
            chartSeries: {
                revenue: {
                    type: 'money',
                    series: [{
                        dataKey: 'potential_revenue',
                        color: '#C2C5F0',
                        label: $t('Money awaiting in abandoned carts')
                    }, {
                        dataKey: 'recovered_revenue',
                        color: '#8D93F9',
                        label: $t('Money made of recovered carts')
                    }]
                },
                efficiency: {
                    type: 'count',
                    series: [{
                        dataKey: 'sent_total',
                        color: '#9E7CE5',
                        label: $t('Sent emails')
                    }, {
                        dataKey: 'restored_total',
                        color: '#5860F3',
                        label: $t('Recovered carts')
                    }, {
                        dataKey: 'placed_total',
                        color: '#8D93F9',
                        label: $t('Actual purchases')
                    }]
                },
                rate: {
                    isPieChart: true
                }
            },
            chart: null,
            chartsData: {},
            listens: {
                '${ $.provider }:reloaded': 'onDataReloaded'
            }
        },

        initObservable: function () {
            return this._super()
                .observe({
                    activeReport: this.reports.rate
                });
        },

        initChartElement: function (element) {
            this.chartContainer = element;
            this.useReport(this.reports.rate);
        },

        useReport: function (name) {
            if (this.chart) {
                this.chart.dispose();
                this.container.dispose();
            }

            this.activeReport(name);

            if (this.chartSeries[name].isPieChart) {
                this.renderPieChart();

                return;
            }

            this.renderXYChart();
        },

        renderXYChart: function () {
            this.container = chart.createContainer(this.chartContainer);
            this.chart = this.container.createChild(am4charts.XYChart);

            var categoryAxis = this.chart.xAxes.push(new am4charts.CategoryAxis()),
                valueAxis = this.chart.yAxes.push(new am4charts.ValueAxis()),
                type = this.chartSeries[this.activeReport()].type;

            am4core.useTheme(am4themes_animated);

            this.container.paddingBottom = 20;
            this.chart.width = am4core.percent(40);
            this.chart.data = [{
                'type': type,
                'value': 0
            }];

            categoryAxis.dataFields.category = "type";
            categoryAxis.renderer.disabled = true;

            valueAxis.renderer.labels.template.fill = "#363636";
            valueAxis.min = 0;

            this.chartSeries[this.activeReport()].series.forEach(function (item) {
                chart.renderSeries(
                    this.chart,
                    type,
                    this.chartsData[item.dataKey],
                    item.color,
                    item.label
                );
            }.bind(this));

            chart.renderLegends(this.chart, this.container);
            this.chart.legend.labels.template.truncate = false;
        },

        renderPieChart: function () {
            this.container = chart.createContainer(this.chartContainer);
            this.chart = this.container.createChild(am4charts.PieChart);

            var series = this.chart.series.push(new am4charts.PieSeries()),
                ratedTotal;

            am4core.useTheme(am4themes_animated);

            this.container.paddingBottom = 10;

            this.chart.width = am4core.percent(40);
            this.chart.hiddenState.properties.opacity = 0;
            this.chart.radius = am4core.percent(80);
            this.chart.innerRadius = am4core.percent(30);
            this.chart.startAngle = 180;
            this.chart.endAngle = 360;

            ratedTotal = this.chartsData.rated_total || 0;
            this.chart.data = [
                {
                    rateLabel: $t('Abandoned Carts'),
                    rateValue: ratedTotal.toString().replace('%', '')
                },
                {
                    rateLabel: $t('Orders'),
                    rateValue: 100 - ratedTotal.toString().replace('%', '')
                }
            ];

            series.dataFields.category = "rateLabel";
            series.dataFields.value = "rateValue";
            series.slices.template.cornerRadius = 3;
            series.slices.template.innerCornerRadius = 3;
            series.slices.template.stroke = am4core.color("#fff");
            series.slices.template.strokeWidth = 3;
            series.slices.template.inert = true;
            series.slices.template.tooltipText = "[bold]{category}:[/] [font-size:14px]{value}%";
            series.ticks.template.disabled = true;
            series.labels.template.disabled = true;
            series.alignLabels = false;
            series.legendSettings.labelText = "[#363636]{rateLabel}[/]";
            series.legendSettings.valueText = "[font-size: 20px #363636]{rateValue}%[/]";
            series.hiddenState.properties.startAngle = 180;
            series.hiddenState.properties.endAngle = 180;

            chart.renderLegends(this.chart, this.container);
            this.chart.legend.labels.template.maxWidth = 150;
            this.chart.legend.labels.template.truncate = false;
            this.chart.legend.labels.template.wrap = true;
        },

        onDataReloaded: function () {
            this.useReport(this.activeReport());
        }
    });
});
