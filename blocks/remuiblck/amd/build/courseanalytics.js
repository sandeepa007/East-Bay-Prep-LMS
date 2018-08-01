//* jshint ignore:start */
define(['theme_remui/Chart'], function (Chart, pieprogress, PieProgress, log) {


    var analysisChart = null;
    /* Course Analytics Block */
    var analysisBar;
    function createAnalysisChart() {

        var course_id = jQuery('#quiz_overview #coursecategorylist option:selected').data('id');
        jQuery.ajax({
            type: "GET",
            async: true,
            url: M.cfg.wwwroot + '/theme/remui/request_handler.php?action=ger_course_anlytics_ajax&courseid=' + course_id,
            success: function (response) {
                analysisBar = response;
                if (analysisChart !== null) {
                    analysisChart.destroy();
                }

                if (response.error) {
                    jQuery("#highestactivity").html("");
                    jQuery("#lowestactivity").html("");

                    jQuery("#highestgrade").html("0");
                    jQuery("#lowestgrade").html("0");
                    jQuery("#averagegrade").html("0");
                } else {
                    jQuery("#highestactivity").html(analysisBar.maxactivityname);
                    jQuery("#lowestactivity").html(analysisBar.minactivityname);

                    jQuery("#highestgrade").html(analysisBar.highest);
                    jQuery("#lowestgrade").html(analysisBar.lowest);
                    jQuery("#averagegrade").html(analysisBar.average);
                }

                var context = jQuery("#analysischart").get(0).getContext("2d");

                context.canvas.height = 400;
                var analysisData = {
                    labels: analysisBar.labels,
                    datasets: analysisBar.datasets
                };
                analysisChart = new Chart(context, {
                    type: 'bar',
                    data: analysisData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        tooltips: {
                            enabled: true
                        },
                        hover: {
                            animationDuration: 0
                        },
                        layout: {
                            padding: {
                                top: 20
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                fontSize: 12
                            }

                        },
                        animation: {
                            duration: 1,
                            onComplete: function () {
                                var chartInstance = this.chart,
                                    ctx = chartInstance.ctx;
                                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';

                                this.data.datasets.forEach(function (dataset, i) {
                                    var meta = chartInstance.controller.getDatasetMeta(i);
                                    if (meta.hidden != true) {
                                        meta.data.forEach(function (bar, index) {
                                            var data = dataset.data[index];
                                            ctx.fillText(data, bar._model.x, bar._model.y - 5);
                                        });
                                    }
                                });
                            }
                        },
                        scales:
                        {
                            xAxes: [{
                                display: false,
                                gridLines: {
                                    display: true,
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                },
                                gridLines: {
                                    display: true
                                },
                            }]
                        }
                    }
                });
            },
            error: function (xhr, status, error) {
                jQuery('div#analysis-chart-area').hide();
            }
        });
    }
    if (jQuery('#analysischart').length) {
        jQuery('#quiz_overview #coursecategorylist').on('change', function () {
            createAnalysisChart();
        });
        createAnalysisChart();
    }
    /* End Course Analytics Block */

});