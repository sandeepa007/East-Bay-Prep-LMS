<?php
// This file is part of mod_checkmark for Moodle - http://moodle.org/
//
// It is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * mod_form.php moodleform for checkmark-settings
 *
 * @package   mod_checkmark
 * @author    Philipp Hager
 * @copyright 2014 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// It must be included from a Moodle page!
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/checkmark/locallib.php');

/**
 * This class contains the instance's settings formular.
 *
 * @package   mod_checkmark
 * @author    Philipp Hager
 * @copyright 2014 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_checkmark_mod_form extends moodleform_mod {
    /** @var object */
    protected $_checkmarkinstance = null;
    /** @var int */
    protected $update = 0;
    /** @var object */
    protected $cm = null;
    /** @var object */
    protected $submissioncount = null;

    /** Defines checkmark instance settings form */
    public function definition() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        $this->update = optional_param('update', 0, PARAM_INT);
        $this->cm = empty($this->update) ? null : get_coursemodule_from_id('', $this->update, 0, false, MUST_EXIST);
        $this->submissioncount = empty($this->update) ? 0 : checkmark_count_real_submissions($this->cm);

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('checkmarkname', 'checkmark'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('description', 'checkmark'));

        $mform->addElement('header', 'availability', get_string('availability', 'assign'));
        $mform->setExpanded('availability', true);

        $name = get_string('availabledate', 'checkmark');
        $mform->addElement('date_time_selector', 'timeavailable', $name, array('optional' => true));
        $mform->addHelpButton('timeavailable', 'availabledate', 'checkmark');
        $mform->setDefault('timeavailable', time());

        $name = get_string('duedate', 'checkmark');
        $mform->addElement('date_time_selector', 'timedue', $name, array('optional' => true));
        $mform->addHelpButton('timedue', 'duedate', 'checkmark');
        $mform->setDefault('timedue', date('U', strtotime('+1week 23:55', time())));

        $name = get_string('cutoffdate', 'checkmark');
        $mform->addElement('date_time_selector', 'cutoffdate', $name, array('optional' => true));
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'checkmark');
        $mform->setDefault('cutoffdate', date('U', strtotime('+1week 23:55', time())));

        $name = get_string('gradingdue', 'checkmark');
        $mform->addElement('date_time_selector', 'gradingdue', $name, array('optional' => true));
        $mform->addHelpButton('gradingdue', 'gradingdue', 'checkmark');
        $mform->setDefault('gradingdue', date('U', strtotime('+5weeks 23:55', time())));

        $name = get_string('alwaysshowdescription', 'checkmark');
        $mform->addElement('advcheckbox', 'alwaysshowdescription', $name);
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'checkmark');
        $mform->setDefault('alwaysshowdescription', 1);
        $mform->hideIf('alwaysshowdescription', 'timeavailable[enabled]', 'notchecked');

        $this->add_checkmark_elements();

        $this->add_attendance_elements();

        $this->add_presentation_elements();

        $coursecontext = context_course::instance($COURSE->id);
        plagiarism_get_form_elements_module($mform, $coursecontext);

        $this->standard_grading_coursemodule_elements();

        $this->standard_coursemodule_elements();

        if ($this->submissioncount) {
            $mform->freeze('grade');
            $mform->freeze('examplecount');
            $mform->freeze('examplestart');
            $mform->freeze('flexiblenaming');
            $mform->freeze('examplenames');
            $mform->freeze('examplegrades');
            $mform->addElement('hidden', 'allready_submit', 'yes');
        } else {
            $mform->addElement('hidden', 'allready_submit', 'no');
        }
        $mform->setType('allready_submit', PARAM_ALPHA);

        $this->add_action_buttons();
    }

    /**
     * Overwritten from moodleform_mod, calls parent
     *
     * We enhance the standard_grading_coursemodule_elements() to set them expanded
     * or alter the default values under certain circumstances.
     */
    public function standard_grading_coursemodule_elements() {
        $mform =& $this->_form;
        parent::standard_grading_coursemodule_elements();
        $mform->addHelpButton('grade', 'grade', 'checkmark');
        $stdexamplecount = get_config('checkmark', 'stdexamplecount');
        if (100 % $stdexamplecount) {
            $mform->setDefault('grade', $stdexamplecount);
        }
        $mform->setExpanded('modstandardgrade');
    }

    /**
     * Adds the elements used for presentation grading!
     */
    public function add_presentation_elements() {
        global $COURSE, $CFG, $DB;
        $mform =& $this->_form;
        $isupdate = !empty($this->_cm);

        $mform->addElement('header', 'presentationheader', get_string('presentationheader', 'checkmark'));

        $gradeoptions = array('isupdate' => $isupdate,
                              'currentgrade' => false,
                              'hasgrades'    => false,
                              'canrescale'   => $this->_features->canrescale,
                              'useratings'   => $this->_features->rating);
        $ingradebook = false;

        if ($isupdate) {
            $gradeitem = grade_item::fetch(array('itemtype' => 'mod',
                                                 'itemmodule' => $this->_cm->modname,
                                                 'iteminstance' => $this->_cm->instance,
                                                 'itemnumber' => CHECKMARK_PRESENTATION_ITEM,
                                                 'courseid' => $COURSE->id));
            $select = "checkmarkid = ? AND (presentationgrade >= 0 OR presentationfeedback IS NOT NULL)";
            $presfbpresent = $DB->record_exists_select('checkmark_feedbacks', $select, array($this->_cm->instance));
            if ($gradeitem) {
                $gradeoptions['currentgrade'] = $gradeitem->grademax;
                $gradeoptions['currentgradetype'] = $gradeitem->gradetype;
                $gradeoptions['currentscaleid'] = $gradeitem->scaleid;
                $gradeoptions['hasgrades'] = $gradeitem->has_grades() || $presfbpresent;
                $ingradebook = true;
            } else {
                // Get gradeoption infos from instance record!
                if ($record = $DB->get_record('checkmark', array('id' => $this->_cm->instance),
                                              'presentationgrading, presentationgrade', MUST_EXIST)) {
                    if (($record->presentationgrading == 0) || ($record->presentationgrade == 0)) {
                        $gradeoptions['currentgradetype'] = 'none';
                        $gradeoptions['currentgrade'] = $CFG->gradepointdefault;
                        $gradeoptions['currentscaleid'] = 0;
                    } else {
                        if ($record->presentationgrade > 0) {
                            $gradeoptions['currentgradetype'] = 'point';
                            $gradeoptions['currentgrade'] = $record->presentationgrade;
                        } else {
                            $gradeoptions['currentradetype'] = 'scale';
                            $gradeoptions['currentgrade'] = $CFG->gradepointdefault;
                            $gradeoptions['currentscaleid'] = -$record->presentationgrade;
                        }
                    }
                } else {
                    $gradeoptions['currentgradetype'] = 'none';
                    $gradeoptions['currentgrade'] = $CFG->gradepointdefault;
                }
                if ($presfbpresent) {
                    $gradeoptions['hasgrades'] = true;
                }
            }
        } else {
            $gradeoptions['currentgrade'] = $CFG->gradepointdefault;
            $gradeoptions['currentgradetype'] = 'none';
        }

        if (key_exists('hasgrades', $gradeoptions) && ($gradeoptions['hasgrades'] == true)) {
            $mform->addElement('hidden', 'presentationfeedbackpresent', 1);
        } else {
            $mform->addElement('hidden', 'presentationfeedbackpresent', 0);
        }
        $mform->setType('presentationfeedbackpresent', PARAM_BOOL);

        $mform->addElement('selectyesno', 'presentationgrading', get_string('presentationgrading', 'checkmark'));
        $mform->addHelpButton('presentationgrading', 'presentationgrading', 'checkmark');
        $mform->setDefault('presentationgrading', 0);
        if (key_exists('hasgrades', $gradeoptions) && ($gradeoptions['hasgrades'] == true)) {
            $mform->freeze('presentationgrading');
        }

        $mform->addElement('modgrade', 'presentationgrade', get_string('presentationgrading_grade', 'checkmark'), $gradeoptions);
        $mform->addHelpButton('presentationgrade', 'presentationgrading_grade', 'checkmark');
        $mform->setDefault('presentationgrade', $CFG->gradepointdefault);
        $mform->hideIf('presentationgrade', 'presentationgrading', 'eq', 0);

        $mform->addElement('selectyesno', 'presentationgradebook', get_string('presentationgradebook', 'checkmark'));
        $mform->addHelpButton('presentationgradebook', 'presentationgradebook', 'checkmark');
        $mform->setDefault('presentationgradebook', $ingradebook);
        $mform->hideIf('presentationgradebook', 'presentationgrading', 'eq', 0);
    }

    /**
     * Helper method adding checkmark elements
     */
    public function add_checkmark_elements() {
        global $OUTPUT, $PAGE;

        $mform = $this->_form;

        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));

        $typetitle = get_string('modulename', 'checkmark');

        $mform->addElement('header', 'typedesc', $typetitle);
        $mform->setExpanded('typedesc');

        $mform->addElement('hidden', 'course', optional_param('course', 0, PARAM_INT));

        $params = new stdClass();
        $params->dividingSymbol = checkmark::DELIMITER;
        $PAGE->requires->js_call_amd('mod_checkmark/settings', 'initializer', array($params));

        $mform->addElement('select', 'resubmit', get_string('allowresubmit', 'checkmark'), $ynoptions);
        $mform->addHelpButton('resubmit', 'allowresubmit', 'checkmark');
        $mform->setDefault('resubmit', 0);

        $mform->addElement('select', 'emailteachers', get_string('emailteachers', 'checkmark'),
                           $ynoptions);
        $mform->addHelpButton('emailteachers', 'emailteachers', 'checkmark');
        $mform->setDefault('emailteachers', 0);

        if (!empty($this->update) && $this->submissioncount) {
            $mform->addElement('html', $OUTPUT->notification(get_string('elements_disabled', 'checkmark'), 'notifymessage'));
        }
        $mform->addElement('text', 'examplecount', get_string('numberofexamples', 'checkmark'), array('id' => 'id_examplecount'));
        // We're going to clean them by ourselves...
        $mform->setType('examplecount', PARAM_INT);
        $mform->addHelpButton('examplecount', 'numberofexamples', 'checkmark');
        $mform->hideIf('examplecount', 'flexiblenaming', 'checked');
        $stdexamplecount = get_config('checkmark', 'stdexamplecount');
        $mform->setDefault('examplecount', $stdexamplecount);

        $mform->addElement('text', 'examplestart', get_string('firstexamplenumber', 'checkmark'));
        // We're going to clean them by ourselves...
        $mform->setType('examplestart', PARAM_INT);
        $mform->addHelpButton('examplestart', 'firstexamplenumber', 'checkmark');
        $mform->hideIf('examplestart', 'flexiblenaming', 'checked');
        $stdexamplestart = get_config('checkmark', 'stdexamplestart');
        $mform->setDefault('examplestart', $stdexamplestart);

        $mform->addElement('text', 'exampleprefix', get_string('exampleprefix', 'checkmark'));
        $mform->setType('exampleprefix', PARAM_TEXT);
        $mform->addHelpButton('exampleprefix', 'exampleprefix', 'checkmark');
        $mform->setDefault('exampleprefix', get_string('strexample', 'checkmark').' ');
        $mform->setAdvanced('exampleprefix');

        $mform->addElement('advcheckbox', 'flexiblenaming', get_string('flexiblenaming', 'checkmark'),
                           get_string('activateindividuals', 'checkmark'), array('id' => 'id_flexiblenaming', 'group' => 1),
                           array('0', '1'));
        $mform->addHelpButton('flexiblenaming', 'flexiblenaming', 'checkmark');

        $mform->setAdvanced('flexiblenaming');

        $mform->addElement('text', 'examplenames', get_string('examplenames', 'checkmark').' ('.checkmark::DELIMITER.')');
        // We clean these by ourselves!
        $mform->setType('examplenames', PARAM_TEXT);
        $mform->addHelpButton('examplenames', 'examplenames', 'checkmark');
        $stdnames = get_config('checkmark', 'stdnames');
        $mform->setDefault('examplenames', $stdnames);

        $mform->hideIf('examplenames', 'flexiblenaming', 'notchecked');
        $mform->setAdvanced('examplenames');

        $mform->addElement('text', 'examplegrades', get_string('examplegrades', 'checkmark').' ('.checkmark::DELIMITER.')',
                           array('id' => 'id_examplegrades'));
        // We clean these by ourselves!
        $mform->setType('examplegrades', PARAM_SEQUENCE);
        $mform->addHelpButton('examplegrades', 'examplegrades', 'checkmark');
        $stdgrades = get_config('checkmark', 'stdgrades');
        $mform->setDefault('examplegrades', $stdgrades);
        $mform->hideIf('examplegrades', 'flexiblenaming', 'notchecked');
        $mform->setAdvanced('examplegrades');
    }

    /**
     * Helper method to add attendance grading items!
     */
    public function add_attendance_elements() {
        $mform =& $this->_form;

        $mform->addElement('header', 'attendance', get_string('attendance', 'checkmark'));

        // Add select: track attendance yes/no?
        $mform->addElement('selectyesno', 'trackattendance', get_string('trackattendance', 'checkmark'));
        $mform->addHelpButton('trackattendance', 'trackattendance', 'checkmark');

        // Add select: attendance influences grade yes/no?
        $mform->addElement('selectyesno', 'attendancegradelink', get_string('attendancegradelink', 'checkmark'));
        $mform->hideIf('attendancegradelink', 'trackattendance', 'eq', 0);
        $mform->addHelpButton('attendancegradelink', 'attendancegradelink', 'checkmark');

        // Add select: save attendance in gradebook yes/no?
        $mform->addElement('selectyesno', 'attendancegradebook', get_string('attendancegradebook', 'checkmark'));
        $mform->hideIf('attendancegradebook', 'trackattendance', 'eq', 0);
        $mform->addHelpButton('attendancegradebook', 'attendancegradebook', 'checkmark');
    }

    /** Needed by plugin checkmark if it includes a filemanager element in the settings form! */
    public function has_instance() {
        return ($this->_instance != null);
    }

    /** Needed by plugin checkmarks if it includes a filemanager element in the settings form! */
    public function get_context() {
        return $this->context;
    }

    /**
     * Returns the module instance (PHP object)
     *
     * @return object checkmark instance
     */
    protected function get_checkmark_instance() {
        global $CFG;

        if ($this->_checkmarkinstance) {
            return $this->_checkmarkinstance;
        }
        require_once($CFG->dirroot.'/mod/checkmark/lib.php');
        $this->checkmarkinstance = new checkmark();
        return $this->checkmarkinstance;
    }

    /**
     * Preprocess form data!
     *
     * Calculate form elements status (examplegrades, examplenames, examplestart,
     * examplecount, flexiblenaming, etc. from given examples for this instance.
     *
     * @param array $defaultvalues (called by reference) values to preprocess and alter if necessary
     */
    public function data_preprocessing(&$defaultvalues) {
        $this->get_checkmark_instance()->form_data_preprocessing($defaultvalues, $this);

        if ($defaultvalues['instance']) {
            $examples = checkmark::get_examples_static($defaultvalues['instance']);
            $flexiblenaming = false;
            $oldname = null;
            $oldgrade = null;
            $names = '';
            $grades = '';
            $examplestart = '';
            $examplecount = count($examples);

            foreach ($examples as $example) {
                $names .= checkmark::DELIMITER . $example->shortname;
                $grades .= checkmark::DELIMITER . $example->grade;
                // First we check the obvious...
                if ($flexiblenaming || preg_match('*[^0-9]*', $example->shortname)) {
                    $flexiblenaming = true;
                } else {
                    if (($oldname == null) && ($oldgrade == null)) {
                        $oldname = $example->shortname;
                        $oldgrade = $example->grade;
                        $names = $example->shortname;
                        $grades = $example->grade;
                        $examplestart = $example->shortname;
                    } else {
                        if ((intval($oldname) + 1 != intval($example->shortname))
                            || (intval($oldgrade) != intval($example->grade))) {
                            $flexiblenaming = true;
                        }
                    }
                    $oldgrade = $example->grade;
                    $oldname = $example->shortname;
                }
            }

            if ($flexiblenaming) {
                $defaultvalues['examplegrades'] = $grades;
                $defaultvalues['examplenames'] = $names;
                $defaultvalues['flexiblenaming'] = true;
                $defaultvalues['examplestart'] = get_config('checkmark', 'stdexamplestart');;
                $defaultvalues['examplecount'] = get_config('checkmark', 'stdexamplecount');;
            } else {
                $defaultvalues['flexiblenaming'] = false;
                $defaultvalues['examplestart'] = $examplestart;
                $defaultvalues['examplecount'] = $examplecount;
            }
        }
    }

    /**
     * Validates current checkmark settings
     *
     * @param array $data data from the module form
     * @param array $files data about files transmitted by the module form
     * @return string[] array of error messages, to be displayed at the form fields
     */
    public function validation($data, $files) {
        // Allow plugin checkmarks to do any extra validation after the form has been submitted!
        $errors = parent::validation($data, $files);

        if ($data['timeavailable'] && $data['timedue']) {
            if ($data['timeavailable'] > $data['timedue']) {
                $errors['timedue'] = get_string('duedatevalidation', 'assign');
            }
        }
        if ($data['timedue'] && $data['cutoffdate']) {
            if ($data['timedue'] > $data['cutoffdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'assign');
            }
        }
        if ($data['timeavailable'] && $data['cutoffdate']) {
            if ($data['timeavailable'] > $data['cutoffdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatefromdatevalidation', 'assign');
            }
        }
        $errors = array_merge($errors, $this->get_checkmark_instance()->form_validation($data));
        return $errors;
    }
}

