<?php

require_once($CFG->dirroot. '/config.php');
require_once($CFG->dirroot . '/my/lib.php');

function xmldb_block_remuiblck_install()
{
    
    global $DB, $PAGE, $CFG;

    $blocklist = [
        'recentfeedback' => '0',
        'recentforums'   => '0',
        'latestmembers'  => '0',
        'courseanlytics' => '0',
        'courseprogress' => '0',
        'addnotes'       => '0',
        'enrolledusers'  => '0',
        'quizattempts'   => '0'
        ];

    $systempage = $DB->get_record('my_pages', array('userid' => null, 'private' => 1));

    $page = new moodle_page();
    $page->set_context(context_system::instance());

    // selecting default region for blocks i.e. content
    $page->blocks->add_region('content');

    // Adding multiple blocks
    foreach ($blocklist as $value) {
        if ($systempage) {
            $page->blocks->add_block('remuiblck', 'content', 5, false, 'my-index', $systempage->id);
        }
    }

    // This will reset the dashboard for everyone using this site.
    my_reset_page_for_all_users(MY_PAGE_PRIVATE, 'my-index');

    // setting flags for blocks addition on dashboard page
    set_config('blocks_flag_instl', serialize($blocklist), 'block_remuiblck');

    return true;
}
