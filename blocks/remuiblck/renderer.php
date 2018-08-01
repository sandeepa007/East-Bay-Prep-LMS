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

defined('MOODLE_INTERNAL') || die;
/**
 * pitchprep block block_pitchprep_reports
 * @package    block_pitchprep_reports
 */
class block_remuiblck_renderer extends plugin_renderer_base
{
    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_mycourses(\block_remuiblck\output\remuiblck_mycourses $obj)
    {
        return $this->render_from_template('block_remuiblck/mycourses', $obj->export_for_template($this));
    }

     /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_blockselector(\block_remuiblck\output\remuiblck_blockselector $obj)
    {
        return $this->render_from_template('block_remuiblck/blockselector', $obj->export_for_template($this));
    }
     /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_coursestats(\block_remuiblck\output\remuiblck_coursestats $obj)
    {
        return $this->render_from_template('block_remuiblck/coursestats', $obj->export_for_template($this));
    }
    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_tasks(\block_remuiblck\output\remuiblck_tasks $obj)
    {
        return $this->render_from_template('block_remuiblck/tasks', $obj->export_for_template($this));
    }
    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_courseprogress(\block_remuiblck\output\remuiblck_courseprogress $obj)
    {
        return $this->render_from_template('block_remuiblck/courseprogress', $obj->export_for_template($this));
    }
     /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_userstats(\block_remuiblck\output\remuiblck_userstats $obj)
    {
        return $this->render_from_template('block_remuiblck/userstats', $obj->export_for_template($this));
    }


     /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_enrolledusers(\block_remuiblck\output\remuiblck_enrolledusers $obj)
    {
        return $this->render_from_template('block_remuiblck/enrolledusers', $obj->export_for_template($this));
    }

     /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_quizattempts(\block_remuiblck\output\remuiblck_quizattempts $obj)
    {
        return $this->render_from_template('block_remuiblck/quizattempts', $obj->export_for_template($this));
    }
    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_remuiblck_courseanlytics(\block_remuiblck\output\remuiblck_courseanlytics $obj)
    {
        return $this->render_from_template('block_remuiblck/courseanlytics', $obj->export_for_template($this));
    }
    /**
    * Return the main content for the block overview.
    *
    * @param main $main The main renderable
    * @return string HTML string
    */
    public function render_remuiblck_latestmembers(\block_remuiblck\output\remuiblck_latestmembers $obj)
    {
        return $this->render_from_template('block_remuiblck/latestmembers', $obj->export_for_template($this));
    }
    /**
    * Return the main content for the block overview.
    *
    * @param main $main The main renderable
    * @return string HTML string
    */
    public function render_remuiblck_addnotes(\block_remuiblck\output\remuiblck_addnotes $obj)
    {
        return $this->render_from_template('block_remuiblck/addnotes', $obj->export_for_template($this));
    }
    /**
    * Return the main content for the block overview.
    *
    * @param main $main The main renderable
    * @return string HTML string
    */
    // public function render_remuiblck_recentsection(\block_remuiblck\output\remuiblck_recentsection $obj)
    // {
    //     return $this->render_from_template('block_remuiblck/recent_section', $obj->export_for_template($this));
    // }
     /**
    * Return the main content for the block overview.
    *
    * @param main $main The main renderable
    * @return string HTML string
    */
    public function render_remuiblck_recentfeedback(\block_remuiblck\output\remuiblck_recentfeedback $obj)
    {
        return $this->render_from_template('block_remuiblck/recent_assignments', $obj->export_for_template($this));
    }
     /**
    * Return the main content for the block overview.
    *
    * @param main $main The main renderable
    * @return string HTML string
    */
    public function render_remuiblck_recentforums(\block_remuiblck\output\remuiblck_recentforums $obj)
    {
        return $this->render_from_template('block_remuiblck/recent_active_forum', $obj->export_for_template($this));
    }
     /**
    * Return the main content for the block overview.
    *
    * @param main $main The main renderable
    * @return string HTML string
    */
    public function render_remuiblck_initialcontent(\block_remuiblck\output\remuiblck_initialcontent $obj)
    {
        return $this->render_from_template('block_remuiblck/initialcontent', $obj->export_for_template($this));
    }
     /**
    * Return the main content for the block overview.
    *
    * @param main $main The main renderable
    * @return string HTML string
    */
    public function render_remuiblck_blckexist(\block_remuiblck\output\remuiblck_blckexist $obj)
    {
        return $this->render_from_template('block_remuiblck/blckexist', $obj->export_for_template($this));
    }
}
