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
 * Includes a renderer to override the default renderer for quiz view .
 *
 * @package   quizaccess
 * @subpackage gradebycategory
 * @copyright 2013 Portsmouth University
 * @author    Jamie Pratt (me@jamiep.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/mod/quiz/accessrule/gradebycategory/mod_quiz_renderer.php');

/**
 * Name needs to be exactly as below for the renderer factory to find it. This 'extension' is just to rename the class.
 */
class theme_remui_mod_quiz_renderer extends quizaccess_gradebycategory_mod_quiz_renderer  {

}