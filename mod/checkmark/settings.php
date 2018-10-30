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
 * settings.php contains admin-Settings for checkmark
 *
 * @package   mod_checkmark
 * @author    Philipp Hager
 * @copyright 2014 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext('checkmark/stdexamplecount',
                                                get_string('strstdexamplecount', 'checkmark'),
                                                get_string('strstdexamplecountdesc', 'checkmark'),
                                                '10'));
    $settings->add(new admin_setting_configtext('checkmark/stdexamplestart',
                                                get_string('strstdexamplestart', 'checkmark'),
                                                get_string('strstdexamplestartdesc', 'checkmark'),
                                                '1'));
    /*
     * TODO tscpr: instead of having the default values hardcoded, you can "calculate" them with
     * the delimiter set in the checkmark class.. just in case :)
     */
    $settings->add(new admin_setting_configtext('checkmark/stdnames',
                                                get_string('strstdnames', 'checkmark'),
                                                get_string('strstdnamesdesc', 'checkmark'),
                                                'a,b,c,d,e,f'));
    $settings->add(new admin_setting_configtext('checkmark/stdgrades',
                                                get_string('strstdgrades', 'checkmark'),
                                                get_string('strstdgradesdesc', 'checkmark'),
                                                '10,10,20,20,20,20'));
    $settings->add(new admin_setting_configtext('checkmark/validmsgtime',
                                                get_string('strvalidmsgtime', 'checkmark'),
                                                get_string('strvalidmsgtimedesc', 'checkmark'),
                                                '2'));

    $settings->add(new admin_setting_configcheckbox('checkmark/showrecentsubmissions',
                                                    get_string('showrecentsubmissions', 'checkmark'),
                                                    get_string('configshowrecentsubmissions', 'checkmark'), 0));

    // Determines the amount of examples in a checkmark instance over which a export warning will be displayed!
    /* Trial'n'error determined maximum amount of standard examples without other columns: 48,
     * so we should have some place left for everything else!*/
    $options = array(0 => get_string('cfg_nowarning', 'checkmark')) + array_combine(range(10, 50, 1), range(10, 50, 1));
    $settings->add(new admin_setting_configselect('checkmark/pdfexampleswarning',
                                                  get_string('cfg_pdfexampleswarning', 'checkmark'),
                                                  get_string('cfg_pdfexampleswarning_desc', 'checkmark'),
                                                  25,
                                                  $options));
}

// TODO: should we remove this link and script someday?
if ($hassiteconfig) { // Needs this condition or there is error on login page!
    $ADMIN->add('grades', new admin_externalpage('checkmark',
            get_string('pluginname', 'checkmark').': '.get_string('checkbrokengradebookgrades', 'checkmark'),
            new moodle_url('/mod/checkmark/db/fixmissinggradebookgrade.php')));
}
