define(['theme_remui/Chart'], function(){
    //----------------------------------
    //- BEGIN PIE CHART - DOUGHNUT
    //----------------------------------

    var myDoughnut = null;
    var barChart = null;
    var analysisChart = null;
    var legendtemplatestr1 = "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%>";
    var legendtemplatestr2 = "<span style=\"background-color:<%=segments[i].fillColor%>\"></span>";
    var legendtemplatestr3 = "<%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>";
    var resp;
    var pieOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke: true,
        //String - The colour of each segment stroke
        segmentStrokeColor: "#fff",
        //Number - The width of each segment stroke
        segmentStrokeWidth: 1,
        //Number - The percentage of the chart that we cut out of the middle
        percentageInnerCutout: 50, // This is 0 for Pie charts
        //Number - Amount of animation steps
        animationSteps: 100,
        //String - Animation easing effect
        /*animation: {
            duration: 2000,
            easing: "easeOutBounce"
        },*/
        //Boolean - Whether we animate the rotation of the Doughnut
        animateRotate: true,
        //Boolean - Whether we animate scaling the Doughnut from the centre
        animateScale: false,
        //Boolean - whether to make the chart responsive to window resizing
        responsive: true,
        // Boolean - whether to maintain the starting aspect ratio or not when responsive,
        // if set to false, will take up entire container
        maintainAspectRatio: true,
        //String - A legend template
        legendTemplate: legendtemplatestr1 + legendtemplatestr2 + legendtemplatestr3,
        //String - A tooltip template
        tooltipTemplate: "<%=value %> <%=label%> users",

        legend: {
            display: false,
        }
    };

    function render_pie_chart() {

        if (myDoughnut !== null) {
            myDoughnut.destroy();
        }

        var pieChartCanvas = jQuery("#pieChartblock").get(0).getContext("2d");

        var doughnutData = {
            labels: resp.labels,
            datasets: [{
                data: resp.data,
                backgroundColor: resp.background_color,
                hoverBackgroundColor: resp.hoverBackground_color,
            }]
        };

        myDoughnut = new Chart(pieChartCanvas, { type: 'doughnut', data: doughnutData, options: pieOptions });
    }


    function createpiechart() {
        var category_id = jQuery('#coursecategorylistblock option:selected').data('id');
        jQuery.ajax({
            type: "GET",
            async: true,
            url: M.cfg.wwwroot + '/theme/remui/request_handler.php?action=get_courses_by_category&categoryid=' + category_id,
            success: function (response) {
                resp = JSON.parse(response);
                if (resp === null) {
                    jQuery('canvas#pieChartblock').hide();
                    jQuery('.enroll-stats-nouserserror').hide();
                    jQuery('.chart-legend').hide();
                    jQuery('.enroll-stats-error').show();
                } else {
                    jQuery('.enroll-stats-error').hide();
                    jQuery('.enroll-stats-nouserserror').hide();
                    jQuery('.chart-legend').show();
                    jQuery('canvas#pieChartblock').show();

                    jQuery('#enrolled_users_stats_block .chart-legend').empty();
                    var colors = ['#2196f3', '#00bcd4', '#009688', '#4caf50', '#8bc34a', '#ffeb3b', '#ff9800', '#f44336', '#9c27b0', '#673ab7', '#3f51b5'];
                    jQuery.each(resp.labels, function (index, value) {
                        jQuery('#enrolled_users_stats_block .chart-legend').append('<li class="list-group-item p-0 pt-5">' + value + ': <span class="badge badge-round" style="background-color:' + colors[index] + ';">' + resp.data[index] + '</span></li>');
                    });

                    render_pie_chart();
                }
            },
            error: function (xhr, status, error) {
                jQuery('canvas#pieChartblock').hide();
                jQuery('.enroll-stats-error').show();
            }
        });
    }

    // update pie chart on category selection
    if (jQuery('#enrolled_users_stats_block select').length) {
        jQuery('#enrolled_users_stats_block select#coursecategorylistblock').on('change', function () {
            createpiechart();
        });
        createpiechart();
    }
    //----------------------------------
    //- END PIE CHART - DOUGHNUT
    //----------------------------------

    
    /* Bar Chart */
    var respBar;

    function createBarChart() {
        var course_id = jQuery('#quiz-course-list option:selected').data('id');
        jQuery.ajax({
            type: "GET",
            async: true,
            url: M.cfg.wwwroot + '/theme/remui/request_handler.php?action=get_courses_for_quiz&courseid=' + course_id,
            success: function (response) {
                respBar = JSON.parse(response);
                if (respBar.datasets === undefined) {
                    jQuery('div#quiz-chart-area').hide();
                    jQuery('.quiz-stats-error').show();
                } else {
                    if (barChart !== null) {
                        barChart.destroy();
                    }
                    var barcontext = jQuery("#barChart").get(0).getContext("2d");
                    barcontext.canvas.height = 400;

                    var barData = {
                        labels: respBar.labels,
                        datasets: respBar.datasets
                    };

                    barChart = new Chart(barcontext, {
                        type: 'bar',
                        data: barData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });

                }
            },
            error: function (xhr, status, error) {
                jQuery('div#quiz-chart-area').hide();
                jQuery('.quiz-stats-error').show();
            }
        });
    }

    if (jQuery('#barChart').length) {
        jQuery('#quiz_stats select#quiz-course-list').on('change', function () {
            createBarChart();
        });
        createBarChart();
    }
    /* End Bar Chart */


});