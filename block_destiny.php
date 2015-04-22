<?php

/**
 * @package    block_destiny
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_destiny extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_destiny');
    }

    public function get_content() {
        return '';
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return true;
    }
}
