(function ($) {

    function errorMsgDialog(msg) {
        var error = $(document.createElement('div'));
        msg='<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> <strong>Error:</strong>'+msg+'</p></div>';
        error.html(msg);
        error.dialog({
            title: "Message",
            autoOpen: false,
            modal: true,
            resizable: false,
            buttons: {
                "OK": function () {
                    $(this).dialog("close");
                }
            },
            open: function (event, ui) {
                $(".ui-widget-overlay").css({
                    opacity: 0.8,
                    filter: "Alpha(Opacity=100)",
                    backgroundColor: "black"
                });
            },
            create: function (event, ui) {
                $(event.target).parent().css('position', 'fixed');
            },
        }).dialog("open");
    }

    function unenrollFromCohort(recId, cohortName, userId, cohortManager) {
        $("#eb-lading-parent").show();
        $.ajax({
            method: "post",
            url: ajaxurl,
            dataType: "json",
            data: {
                'action': 'mucp_unenrol_user',
                '_nonce': jQuery('#mucp-manage-enrol').val(),
                'rec_id': recId,
                'mdl_cohort_id': cohortName,
                'user_id': userId,
                'enrolled_by': cohortManager
            },
            success: function (response) {
                if (response['success']) {
                    if (response['data'] == "OK") {
                        var url = window.location.href + "&unenroll=1";
                        window.location.href = url;
                    } else {
                        errorMsgDialog(response['data']);
                    }
                } else {
                    errorMsgDialog(response['data']);
                }
                $("#eb-lading-parent").hide();

            },
            error: function (response) {
                $("#eb-lading-parent").hide();
                errorMsgDialog(response);
            },
        });
    }


    $('.mucp-manage-enrol-wrap').ready(function () {
        $(".ebbp-cohort-details-link").click(function () {
            var recId = $(this).data('record-id');
            var mdlCohortId = $(this).data('mdl-cohort-id');
            var userId = $(this).data('user-id');
            var cohortManager = $(this).data('cohort-manager');
            var cohortName = $(this).parent();
            cohortName = cohortName.find("p").text();
            $("#eb-lading-parent").show();
            $.ajax({
                method: "post",
                url: ajaxurl,
                dataType: "json",
                data: {
                    'action': 'mucp_cohort_details',
                    '_nonce': jQuery('#mucp-manage-enrol').val(),
                    'enrolled_by': $(this).data('cohort-manager'),
                    'mdl_cohort_id': mdlCohortId,
                    'user_id': userId
                },
                success: function (response) {
                    if (response['success']) {
                        response = response['data'];
                        $("#eb-lading-parent").hide();
                        var title = response['cohort_name'] + " Cohort Details";
                        $("#eb-copany-name").html(response.companyName);
                        $("#eb-manager").html(response.manager);
                        $("#eb-members").html(response.members);
                        $("#eb-courses").html(response.courses);
                        $("#eb-current-user").html(response.currentUser);
                        $("#mucp-cohort-details-dialog").dialog(
                                {
                                    title: title,
                                    autoOpen: false,
                                    modal: true,
                                    minWidth: 500,
                                    maxWidth: 600,
                                    resizable: false,
                                    open: function (event, ui) {
                                        $(".ui-widget-overlay").css({
                                            opacity: 0.8,
                                            filter: "Alpha(Opacity=100)",
                                            backgroundColor: "black"
                                        });
                                        $("#mucp-cohort-details-dialog").css('overflow', 'hidden');
                                    },
                                    create: function (event, ui) {
                                        $(event.target).parent().css('position', 'fixed');
                                    },
                                    buttons: [
                                        {
                                            id: "unenroll_from_cohort",
                                            text: "Unenroll From Cohort",
                                            click: function () {
                                                $("#mucp-cohort-details-dialog").dialog('close');
                                                unenrollFromCohort(recId, mdlCohortId, userId, cohortManager);
                                            },
                                        },
                                    ]
                                }
                        ).dialog("open");
                    } else {
                        $("#eb-lading-parent").hide();
                        errorMsgDialog(response['data']);
                    }
                },
                error: function (response) {
                    $("#eb-lading-parent").hide();
                    errorMsgDialog(response);
                },
            });
        });
    });
})(jQuery);
