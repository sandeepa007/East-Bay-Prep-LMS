<?php

if (empty($moodle_courses_data)) {
    echo '<p class="eb-dtable-error">';
    _e('There is a problem while connecting to moodle server. Please, check your moodle connection or try <a href="javascript:history.go(0)">reloading</a> the page.', 'selective_synchronization');
    echo '</p>';

} else {
?>

<table id='moodle_courses_table' >
<thead>
    <tr>
        <td></td>
        <td></td>
        <td class="filter eb-filter"><?php _e('All Categories', 'selective_synchronization'); ?></td>
    </tr> 
    <tr>
        <th class="dt-center"><input type='checkbox' name='select_all_course' /></th>
        <th class="dt-left"><?php _e('Course Name', 'selective_synchronization'); ?></th>
        <th class="dt-left eb-last-td"><?php _e('Category', 'selective_synchronization'); ?></th>
    </tr>
</thead>
<tbody>
<?php

foreach ($moodle_courses_data as $course_data) {
    /**
             * moodle always returns moodle frontpage as first course,
             * below step is to avoid the frontpage to be added as a course.
             *
             * @var [type]
             */
    if ($course_data->id == 1) {
        continue;
    }

        
    echo '<tr>';
        echo '<td class="dt-center"><input type="checkbox" name="chksel_course" value="'.$course_data->id.'" /></td>';
        echo '<td>'. $course_data->fullname .'</td>';

    foreach ($moodle_category_data as $category) {
        if ($category->id == $course_data->categoryid) {
            if ($category->depth > 1) {
                $ids = explode("/", $category->path);
                echo '<td class="eb-last-td">';

                foreach ($ids as $id) {
                    foreach ($moodle_category_data as $category) {
                        if ($category->id == $id) {
                            echo $category->name;
                            if (end($ids) !== $id) {
                                echo ' / ';
                            }
                                        
                            if (! in_array($category->name, $category_list)) {
                                array_push($category_list, $category->name);
                            }
                            break;
                        }
                    }
                }
                echo '</td>';
            } else {
                echo '<td class="eb-last-td">'. $category->name .'</td>';
                if (! in_array($category->name, $category_list)) {
                    array_push($category_list, $category->name);
                }
            }

            break;
        }
    }
        echo '</tr>';
}

    ?>
	<tfoot>

    <tr>
        <th class="dt-center"><input type='checkbox' name='select_all_course' /></th>
        <th class="dt-left"><?php _e('Course Name', 'selective_synchronization'); ?></th>
        <th class="dt-left eb-last-td"><?php _e('Category', 'selective_synchronization'); ?></th>
    </tr>
	</tfoot>
	</tbody>
</table>

<?php
}