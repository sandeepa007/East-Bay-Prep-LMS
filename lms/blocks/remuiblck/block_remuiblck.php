<?php
require_once($CFG->dirroot.'/blocks/remuiblck/lib.php');
class block_remuiblck extends block_base
{
    public function init()
    {
        $this->title = get_string('remuiblck', 'block_remuiblck');
    }
    // The PHP tag and the curly bracket for the class definition
    // will only be closed after there is another function added in the next section.

    public function get_content()
    {
        global $CFG, $PAGE, $DB, $USER;
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content =  new stdClass;
        $blockopt = 'noconfig';
        // See if the configuration is set for block
        if ($this->config) {
            $blockopt = $this->config->block;
        } else {
            // Get the Plugin config array for default dashboar content
            $blockstate = unserialize(get_config('block_remuiblck', 'blocks_flag_instl'));

            // loop through the data array from Plugin config
            foreach ($blockstate as $key => $value) {
                // check if the flag is not set yet now
                if ($value == '0') {
                    // Creating config object for current block
                    $configobj = new stdClass;
                    $configobj->block = $key;
                    $configobj->scroll = 'unlocked';

                    // Updating current block config
                    $DB->update_record('block_instances', ['id' => $this->instance->id, 'configdata' => base64_encode(serialize($configobj)), 'timemodified' => time()]);

                    $blockopt = $key;

                    // change flag value in config array
                    $blockstate[$key] = '1';

                    // updating the config array for plugin
                    set_config('blocks_flag_instl', serialize($blockstate), 'block_remuiblck');
                    break;
                }
            }
        }
 
        $systempage = $DB->get_record('my_pages', array('userid' => $USER->id, 'private' => 1));
        if ($systempage) {
            $instances = $DB->get_records('block_instances', array('blockname'=>'remuiblck', 'pagetypepattern'=>'my-index', 'subpagepattern' =>$systempage->id));

            foreach ($instances as $key => $inst) {
                if (isset($inst->configdata)) {
                    $curblock = unserialize(base64_decode($inst->configdata));
                    if ($this->instance->id != $inst->id) {
                        if ($blockopt == $curblock->block) {
                            $blockopt = 'blckexist';
                            break;
                        }
                    }
                }
            }
        }
        $this->title = get_string($blockopt, 'block_remuiblck');
        $this->content->text = generate_block($blockopt);
        return $this->content;
    }

    public function instance_allow_multiple()
    {
        return true;
    }

    public function applicable_formats()
    {
        return array('my' => true);
    }

    /*
     the following line must be added to the /blocks/simplehtml/block_simplehtml.php file in order to enable global configuration:
     This line tells Moodle that the block has a settings.php file.
    */
    function has_config()
    {
        return true;
    }

    public function get_content_for_output($output)
    {
        global $CFG;

        $bc = new block_contents($this->html_attributes());
        $bc->attributes['data-block'] = $this->name();


        if (isset($this->config->block)) {
            $bc->attributes['data-subname'] = $this->config->block;
        }

        if (!isset($this->config->block) && 'remuiblck' == $this->name()) {
            $bc->attributes['data-subname'] = 'courseprogress';
        }

        $bc->blockinstanceid = $this->instance->id;
        $bc->blockpositionid = $this->instance->blockpositionid;

        if ($this->instance->visible) {
            $bc->content = $this->formatted_contents($output);
            if (!empty($this->content->footer)) {
                $bc->footer = $this->content->footer;
            }
        } else {
            $bc->add_class('invisible');
        }

        if (!$this->hide_header()) {
            $bc->title = $this->title;
        }

        if (empty($bc->title)) {
            $bc->arialabel = new lang_string('pluginname', get_class($this));
            $this->arialabel = $bc->arialabel;
        }

        if ($this->page->user_is_editing()) {
            $bc->controls = $this->page->blocks->edit_controls($this);
        } else {
            // we must not use is_empty on hidden blocks
            if ($this->is_empty() && !$bc->controls) {
                return null;
            }
        }

        if (empty($CFG->allowuserblockhiding)
                || (empty($bc->content) && empty($bc->footer))
                || !$this->instance_can_be_collapsed()) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        } elseif (get_user_preferences('block' . $bc->blockinstanceid . 'hidden', false)) {
            $bc->collapsible = block_contents::HIDDEN;
        } else {
            $bc->collapsible = block_contents::VISIBLE;
        }

        if ($this->instance_can_be_docked() && !$this->hide_header()) {
            $bc->dockable = true;
        }

        $bc->annotation = ''; // TODO MDL-19398 need to work out what to say here.

        return $bc;
    }
}
