define([
    'uiElement',
    'Amasty_Acart/vendor/amcharts/core.min',
    'Amasty_Acart/vendor/amcharts/charts.min',
    'Amasty_Acart/vendor/amcharts/themes/animated.min'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            listens: {
                products: 'onProductReloaded'
            }
        },

        initObservable: function () {
            return this._super()
                .observe({
                    products: []
                });
        },

        onProductReloaded: function (data) {
            if (this.chart) {
                this.chart.dispose();
            }

            this.initTopReasonChart(data)
        },

        initTopReasonChart: function (data) {
            var categoryAxis,
                valueAxis,
                series,
                valueLabel,
                markerTemplate,
                marker,
                columnTemplate;

            if (!data) {
                data = [];
            }

            this.chart = am4core.create(this.chartElement, am4charts.XYChart);

            am4core.useTheme(am4themes_animated);

            this.chart.maskBullets = false;
            this.chart.paddingBottom = 30;
            this.chart.paddingLeft = 5;
            this.chart.width = am4core.percent(70);
            data = data.sort(this.sortFunction);
            if (data.length > 5) {
                data.length = 5;
            }

            this.chart.data = data;

            categoryAxis = this.chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "product_name";
            categoryAxis.renderer.grid.template.disabled = true;
            categoryAxis.renderer.line.strokeOpacity = 1;
            categoryAxis.renderer.line.strokeWidth = 1;
            categoryAxis.renderer.line.stroke = '#b9b9b9';
            categoryAxis.renderer.labels.template.disabled = true;

            valueAxis = this.chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.grid.template.disabled = true;
            valueAxis.renderer.line.strokeOpacity = 1;
            valueAxis.renderer.line.strokeWidth = 1;
            valueAxis.renderer.line.stroke = '#b9b9b9';
            valueAxis.renderer.labels.template.disabled = true;

            series = this.chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "count";
            series.dataFields.categoryX = "product_name";
            series.name = "count";
            series.columns.template.height = am4core.percent(70);

            columnTemplate = series.columns.template;
            columnTemplate.column.cornerRadiusTopLeft = 10;
            columnTemplate.column.cornerRadiusTopRight = 10;
            columnTemplate.strokeWidth = 0;
            columnTemplate.fill = '#5f92f7';
            columnTemplate.tooltipText = "[fill:#fff]{categoryX}:[/] [bold;fill:#fff]{valueY}[/]";

            valueLabel = series.bullets.push(new am4charts.LabelBullet());
            valueLabel.label.text = "{count}";
            valueLabel.label.dy = -10;

            series.columns.template.adapter.add("fill", function (fill, target) {
                return this.chart.colors.getIndex(target.dataItem.index);
            }.bind(this));

            this.chart.legend = new am4charts.Legend();
            this.chart.legend.itemContainers.template.togglable = false;
            this.chart.legend.position = 'right';
            this.chart.legend.width = am4core.percent(50);

            series.events.on("ready", function () {
                var legendData = [];
                series.columns.each(function (column) {
                    legendData.push({
                        name: column.dataItem.categoryX,
                        fill: column.fill,
                        count: column.dataItem.valueY
                    });
                });
                this.chart.legend.data = legendData;
            }.bind(this));

            this.chart.legend.itemContainers.template.paddingTop = 5;
            this.chart.legend.itemContainers.template.paddingBottom = 5;

            //temporarily disable clickability
            this.chart.legend.itemContainers.template.clickable = false;
            this.chart.legend.itemContainers.template.focusable = false;
            this.chart.legend.labels.template.text = "{name}: [bold]{count}[/]";
            this.chart.legend.itemContainers.template.cursorOverStyle = am4core.MouseCursorStyle.default;

            markerTemplate = this.chart.legend.markers.template;
            markerTemplate.width = 10;
            markerTemplate.height = 10;

            marker = this.chart.legend.markers.template.children.getIndex(0);
            marker.cornerRadius(15, 15, 15, 15);
        },

        sortFunction: function (a, b) {
            return (b.total - a.total);
        },

        initChartElement: function (element) {
            this.chartElement = element;
        }
    });
});
