<?php
require_once $CFG->dirroot."/blocks/remuiblck/classes/output/renderable.php";
function generate_block($block)
{
    // Get Renderable object
    switch ($block) {
        /***************************/
        /*
        * This are the blocks which will come from theme
        *
        */
        // case 'mycourses':
        //     $renderable = new \block_remuiblck\output\remuiblck_mycourses('mycourses');
        //     break;
        // case 'coursestats':
        //     // Coursestats block which shows the number of courses enrolled
        //     // and completion status of activities
        //     // visible by all users ( NO capability check required )
        //      $renderable = new \block_remuiblck\output\remuiblck_coursestats('coursestats');
        //     break;
        // case 'tasks':
        //     $renderable = new \block_remuiblck\output\remuiblck_tasks('tasks');
        //     break;
        case 'noconfig':
            $renderable = new \block_remuiblck\output\remuiblck_initialcontent('initialcontent');
            break;
        case 'blckexist':
            $renderable = new \block_remuiblck\output\remuiblck_blckexist('blckexist');
            break;
        case 'courseprogress':
            $renderable = new \block_remuiblck\output\remuiblck_courseprogress('courseprogress');
            break;
        // case 'userstats':
        //     $renderable = new \block_remuiblck\output\remuiblck_userstats('userstats');
        //     break;
        case 'enrolledusers':
            $renderable = new \block_remuiblck\output\remuiblck_enrolledusers('enrolledusers');
            break;
        case 'quizattempts':
            $renderable = new \block_remuiblck\output\remuiblck_quizattempts('quizattempts');
            break;
        case 'courseanlytics':
            $renderable = new \block_remuiblck\output\remuiblck_courseanlytics('courseanlytics');
            break;
        case 'latestmembers':
            $renderable = new \block_remuiblck\output\remuiblck_latestmembers('latestmembers');
            break;
        case 'addnotes':
            $renderable = new \block_remuiblck\output\remuiblck_addnotes('addnotes');
            break;
        // case 'recentsection':
        //     $renderable = new \block_remuiblck\output\remuiblck_recentsection('recentsection');
        //     break;
        case 'recentfeedback':
            $renderable = new \block_remuiblck\output\remuiblck_recentfeedback('recentfeedback');
            break;
        case 'recentforums':
            $renderable = new \block_remuiblck\output\remuiblck_recentforums('recentforums');
            break;
        default:
            return '';
    }
    
    return get_content_from_renderer('block_remuiblck', $renderable);
}

function get_content_from_renderer($block, $renderable)
{
    global $PAGE;
    $renderer = $PAGE->get_renderer($block);
    $content = $renderer->render($renderable);
    return $content;
}
