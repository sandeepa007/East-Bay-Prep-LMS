define([], function(){
     /* Add Notes Block */

    if (jQuery('.add-notes-select').length) {
        //jQuery('.add-notes-select select option:first-child').attr('disabled', true);
        jQuery('.add-notes-button a').hide();
        jQuery('.select2-studentlist').hide();
        var course_id, student_count, user_id, course_name;

        jQuery('.add-notes-select select').on('change', function () {
            jQuery('.add-notes-button a').hide();
            course_id = jQuery(this).children(":selected").attr("id");
            course_name = jQuery(this).children(":selected").text();
            if (course_id === undefined) {
                jQuery('.select2-studentlist').empty();
                jQuery('.select2-studentlist').hide();
                return;
            }
            var type = 'userlist';

            jQuery.ajax({
                type: "GET",
                async: true,
                url: M.cfg.wwwroot + '/theme/remui/request_handler.php?action=get_' + type + '&courseid=' + course_id,
                success: function (data) {
                    student = JSON.parse(data);
                    student_count = Object.keys(student).length;
                    jQuery('.select2-studentlist').show();
                    jQuery('.select2-studentlist').empty();
                    if (student_count) {
                        jQuery('.select2-studentlist').append('<option>' + M.util.get_string(
                            "selectastudent", "theme_remui") + ' (' + M.util.get_string("total", "theme_remui") +
                            ': ' + student_count + ')</option>');
                    } else {
                        jQuery('.select2-studentlist').append('<option>' + M.util.get_string("nousersenrolledincourse",
                            "theme_remui", course_name) + '</option>');
                    }

                    jQuery.each(student, function (index, value) {
                        jQuery('.select2-studentlist').append('<option value="' + index + '">' + value.firstname + " " +
                            value.lastname + '</option>');
                    });
                    data = "";
                },
                error: function (xhr, status, error) {
                    jQuery('.select2-studentlist').html('<option>' + error + '</option>');
                }
            });
        });

        jQuery('.select2-studentlist').on('change', function () {
            jQuery('.add-notes-button a').show();
            user_id = jQuery(this).find('option:selected').val();
            var notes_link = M.cfg.wwwroot + '/notes/edit.php?courseid=' + course_id +
                '&userid=' + user_id + '&publishstate=site';
            jQuery('.add-notes-button .site-note').attr('href', notes_link);
            notes_link = M.cfg.wwwroot + '/notes/edit.php?courseid=' + course_id +
                '&userid=' + user_id + '&publishstate=public';
            jQuery('.add-notes-button .course-note').attr('href', notes_link);
            notes_link = M.cfg.wwwroot + '/notes/edit.php?courseid=' + course_id +
                '&userid=' + user_id + '&publishstate=draft';
            jQuery('.add-notes-button .personal-note').attr('href', notes_link);
        });
    }
    /* End - Add Notes Block */
});