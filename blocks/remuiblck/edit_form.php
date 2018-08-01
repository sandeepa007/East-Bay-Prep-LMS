<?php
 
class block_remuiblck_edit_form extends block_edit_form
{
 
    protected function specific_definition($mform)
    {
        // global $DB, $USER;
        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

         $options = array(
            // 'mycourses'   => get_string('mycourses', 'block_remuiblck'),
            // 'coursestats' => get_string('coursestats', 'block_remuiblck'),
            // 'tasks'       => get_string('tasks', 'block_remuiblck'),
            'courseprogress' => get_string('courseprogress', 'block_remuiblck'),
            // 'userstats'      => get_string('userstats', 'block_remuiblck'),
            'enrolledusers'  => get_string('enrolledusers', 'block_remuiblck'),
            'quizattempts'   => get_string('quizattempts', 'block_remuiblck'),
            'courseanlytics' => get_string('courseanlytics', 'block_remuiblck'),
            'latestmembers'  => get_string('latestmembers', 'block_remuiblck'),
            'addnotes'       => get_string('addnotes', 'block_remuiblck'),
            // 'recentsection'  => get_string('recentsection', 'block_remuiblck'),
            'recentfeedback' => get_string('recentfeedback', 'block_remuiblck'),
            'recentforums'   => get_string('recentforums', 'block_remuiblck'),
        );

        // $systempage = $DB->get_record('my_pages', array('userid' => $USER->id, 'private' => 1));
       
        // $instances = $DB->get_records('block_instances', array('blockname'=>'remuiblck', 'pagetypepattern'=>'my-index', 'subpagepattern' =>$systempage->id));
        // foreach ($instances as $key => $inst) {
        //     $curblock = unserialize(base64_decode($inst->configdata));
            
        //     unset($options[$curblock->block]);
        // }
       
        $mform->addElement('select', 'config_block', get_string('remuiblck', 'block_remuiblck'), $options, array());

        $options = array(
            'unlock'  => get_string('unlocked', 'block_remuiblck'),
            'lock'    => get_string('locked', 'block_remuiblck'),
        );
        $mform->addElement('select', 'config_scroll', get_string('block_scroll', 'block_remuiblck'), $options, array());

        // $options = array(
        //     '1'  => '1',
        //     '2'  => '2',
        //     '3'  => '3',
        //     '4'  => '4',
        //     '5'  => '5',
        //     '6'  => '6',
        //     '7'  => '7',
        //     '8'  => '8',
        //     '9'  => '9',
        //     '10' => '10',
        //     '11' => '11',
        //     '12' => '12',
        // );
        // $mform->addElement('select', 'config_width', get_string('block_width', 'block_remuiblck'), $options, array());
        // $mform->addElement('select', 'config_height', get_string('block_height', 'block_remuiblck'), $options, array());
    }
}
