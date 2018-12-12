<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the renderer for the quiz module.
 *
 * @package   mod_quiz
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_remui\output;
use html_writer;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');
/**
 * The renderer for the quiz module.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_renderer extends \mod_quiz_renderer {

    /**
     * Return the HTML of the quiz timer.
     * @return string HTML content.
     */
    public function countdown_timer(\quiz_attempt $attemptobj, $timenow) {

        $timeleft = $attemptobj->get_time_left_display($timenow);
        $output = '';
        if ($timeleft !== false) {
            $ispreview = $attemptobj->is_preview();
            $timerstartvalue = $timeleft;
            if (!$ispreview) {
                // Make sure the timer starts just above zero. If $timeleft was <= 0, then
                // this will just have the effect of causing the quiz to be submitted immediately.
                $timerstartvalue = max($timerstartvalue, 1);
            }
            $this->initialise_timer($timerstartvalue, $ispreview);
        }

        $output .= html_writer::tag('div', get_string('timeleft', 'quiz') . ' ' .
                html_writer::tag('span', '', array('id' => 'quiz-time-left')),
                array('id' => 'quiz-timer', 'role' => 'timer',
                    'aria-atomic' => 'true', 'aria-relevant' => 'text'));

        if(isset($timerstartvalue) && $timerstartvalue != null) {
            $output .= '<div id="quiztimer" class="quiztimer" data-timer="'.($timerstartvalue-2).'"></div>';
        }

        return $output;
    }
    public function attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id,
            $nextpage) {
        $output = '';
        $output .= $this->header();
        $output .= $this->quiz_notices($messages);
        $output .= $this->attempt_form($attemptobj, $page, $slots, $id, $nextpage);
        $output .= $this->footer();
        return $output;
    }
    /**
     * Renders each question
     *
     * @param quiz_attempt $attemptobj instance of quiz_attempt
     * @param bool $reviewing
     * @param array $slots array of intgers relating to questions
     * @param int $page current page number
     * @param bool $showall if true shows attempt on single page
     * @param mod_quiz_display_options $displayoptions instance of mod_quiz_display_options
     */
    //public function questions(\quiz_attempt $attemptobj, $reviewing, $slots, $page, $showall,                              \mod_quiz_display_options $displayoptions) {
       /* $number  =1;
        $output = '';
        
        
        foreach ($slots as $slot) {
            $qa = $attemptobj->get_question_attempt($slot );


            $output .= $this->render_question($qa, $slot);
        }
        return $output;
    }
    public function render_question($qa , $number, \qbehaviour_renderer $behaviouroutput =  NULL, \qtype_renderer $qtoutput = NULL, question_display_options $options = NULL ){
        $output = '';
        $output .= html_writer::start_tag('div', array(
            'id' => 'q' . $qa->get_slot(),
            'class' => implode(' ', array(
                'que',
                $qa->get_question()->qtype->name(),
                $qa->get_behaviour_name(),
                $qa->get_state_class($options->correctness && $qa->has_marks()),
            ))
        ));

        $output .= html_writer::tag('div',
                $this->info($qa, $behaviouroutput, $qtoutput, $options, $number),
                array('class' => 'info'));

        $output .= html_writer::start_tag('div', array('class' => 'content'));

        $output .= html_writer::tag('div',
                $this->add_part_heading($qtoutput->formulation_heading(),
                    $this->formulation($qa, $behaviouroutput, $qtoutput, $options)),
                array('class' => 'formulation clearfix'));
        $output .= html_writer::nonempty_tag('div',
                $this->add_part_heading(get_string('feedback', 'question'),
                    $this->outcome($qa, $behaviouroutput, $qtoutput, $options)),
                array('class' => 'outcome clearfix'));
        $output .= html_writer::nonempty_tag('div',
                $this->add_part_heading(get_string('comments', 'question'),
                    $this->manual_comment($qa, $behaviouroutput, $qtoutput, $options)),
                array('class' => 'comment clearfix'));
        $output .= html_writer::nonempty_tag('div',
                $this->response_history($qa, $behaviouroutput, $qtoutput, $options),
                array('class' => 'history clearfix'));

        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        return $output;*/
   // }
    protected function info($qa,  $behaviouroutput,
             $qtoutput,  $options, $number) {
        $output = '';
        $output .= $this->number($number);
        $output .= $this->status($qa, $behaviouroutput, $options);
        //$output .= $this->mark_summary($qa, $behaviouroutput, $options);
        //$output .= $this->question_flag($qa, $options->flags);
        //$output .= $this->edit_question_link($qa, $options);
        return $output;
    }
    /**
     * Generate the display of the question number.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return HTML fragment.
     */
    protected function number($number) {
        if (trim($number) === '') {
            return '';
        }
        $numbertext = '';
        if (trim($number) === 'i') {
            $numbertext = get_string('information', 'question');
        } else {
            $numbertext = get_string('questionx', 'question',
                    html_writer::tag('span', $number, array('class' => 'qno')));
        }
        return html_writer::tag('h3', $numbertext, array('class' => 'no'));
    }
    /**
     * Generate the display of the status line that gives the current state of
     * the question.
     * @param question_attempt $qa the question attempt to display.
     * @param qbehaviour_renderer $behaviouroutput the renderer to output the behaviour
     *      specific parts.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return HTML fragment.
     */
    protected function status(\question_attempt $qa, $behaviouroutput,  $options) {
        return html_writer::tag('div', $qa->get_state_string($options->correctness),
                array('class' => 'state'));
    }
    /**
     * Generate the display of the marks for this question.
     * @param question_attempt $qa the question attempt to display.
     * @param qbehaviour_renderer $behaviouroutput the behaviour renderer, which can generate a custom display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return HTML fragment.
     */
    protected function mark_summary(\question_attempt $qa,  $behaviouroutput,  $options) {
        return html_writer::nonempty_tag('div',
                $behaviouroutput->mark_summary($qa, $this, $options),
                array('class' => 'grade'));
    }
    /**
     * Outputs the table containing data from summary data array
     *
     * @param array $summarydata contains row data for table
     * @param int $page contains the current page number
     */
    public function review_summary_table($summarydata, $page) {
        $summarydata = $this->filter_review_summary_table($summarydata, $page);
        if (empty($summarydata)) {
            return '';
        }

        $output = '';
        $output .= html_writer::start_tag('div', array(
                'class' => 'wholediv1'));
        $output .= html_writer::start_tag('div', array(
                'class' => 'innerdiv'));
        foreach ($summarydata as $rowdata) {
            if ($rowdata['title'] instanceof renderable) {
                $title = $this->render($rowdata['title']);
            } else {
                $title = $rowdata['title'];   //print_r("$rowdata"); exit();
            }

            if ($rowdata['content'] instanceof renderable) {
                $content = $this->render($rowdata['content']);
            } else {
                $content = $rowdata['content'];
            }

            $output .= html_writer::tag('div',
                html_writer::tag('div', $title, array('class' => 'classhead', 'scope' => 'row')) .
                        html_writer::tag('div', $content, array('class' => 'classcontent')),array(
                'class' => 'wholediv')
            );
        }

        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        return $output;  
    }  
    

}
