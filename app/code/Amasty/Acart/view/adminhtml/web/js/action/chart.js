define([
    'Amasty_Acart/vendor/amcharts/core.min',
    'Amasty_Acart/vendor/amcharts/charts.min',
    'Amasty_Acart/vendor/amcharts/themes/animated.min'
], function () {
    'use strict';
    return {
        createContainer: function (id) {
            var container = am4core.create(id, am4core.Container);

            container.layout = "grid";
            container.fixedWidthGrid = false;
            container.width = am4core.percent(100);
            container.height = am4core.percent(100);

            return container;
        },

        renderSeries: function (chart, valueType, value, color, name) {
            var series = chart.series.push(new am4charts.ColumnSeries());

            series.name = name;
            series.dataFields.valueY =  "value";
            series.dataFields.categoryX = "type";
            series.data = [{
                "type": valueType,
                "value": value
            }];
            series.sequencedInterpolation = true;
            series.columns.template.width = am4core.percent(100);
            series.contentWidth = 40;
            series.fill = am4core.color(color);
            series.strokeWidth = 0;
            series.columns.template.tooltipText = "[bold]{name}[/]\n[font-size:14px]{valueY}";
        },

        renderLegends: function (chart, container) {
            var legendContainer = container.createChild(am4core.Container),
                markerTemplate,
                marker;

            legendContainer.width = am4core.percent(60);
            legendContainer.valign = "middle";
            legendContainer.paddingLeft = 25;

            chart.legend = new am4charts.Legend();
            chart.legend.parent = legendContainer;
            chart.legend.labels.template.fill = "#363636";
            chart.legend.useDefaultMarker = true;
            chart.legend.position = "left";

            markerTemplate = chart.legend.markers.template;
            markerTemplate.width = 10;
            markerTemplate.height = 10;

            marker = chart.legend.markers.template.children.getIndex(0);
            marker.cornerRadius(15, 15, 15, 15);
        }
    }
});
