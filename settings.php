<?php

/**
 * @package    block_destiny
 * @copyright  Anthony Kuske <www.anthonykuske.com>
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
