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
 * @package    block_destiny
 * @copyright  2015 Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(
    new admin_setting_configtext(
        'block_destiny/db_host',
        get_string('settings_db_host_title', 'block_destiny'),
        get_string('settings_db_host_desc', 'block_destiny'),
        ''
    )
);

$settings->add(
    new admin_setting_configtext(
        'block_destiny/db_name',
        get_string('settings_db_name_title', 'block_destiny'),
        get_string('settings_db_name_desc', 'block_destiny'),
        ''
    )
);

$settings->add(
    new admin_setting_configtext(
        'block_destiny/db_user',
        get_string('settings_db_user_title', 'block_destiny'),
        get_string('settings_db_user_desc', 'block_destiny'),
        ''
    )
);

$settings->add(
    new admin_setting_configpasswordunmask(
        'block_destiny/db_pass',
        get_string('settings_db_pass_title', 'block_destiny'),
        get_string('settings_db_pass_desc', 'block_destiny'),
        ''
    )
);
