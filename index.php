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

require_once(dirname(dirname(dirname(__FILE__))) .  '/config.php');

// Show page header.
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/destiny');
$PAGE->set_title(get_string('page_title', 'block_destiny'));
$PAGE->set_heading(get_string('page_heading', 'block_destiny'));

echo $OUTPUT->header();

require_login();

// Create Destiny access object.
$destiny = new \block_destiny\local\destiny();

// Who are we going to show...
$idnumbers = $destiny->get_idnumbers();

if (count($idnumbers) > 1) {
    $introtext = get_string('multiple_intro_text', 'block_destiny');
} else {
    $introtext = get_string('intro_text', 'block_destiny');
}

?>

<div class="alert alert-info">
    <i class="fa fa-book pull-left fa-2x"></i>
    <p><?php echo $introtext; ?></p>
</div>

<br/>

<?php

foreach ($idnumbers as $idnumber => $name) {

    echo '<h2>' . $name . '</h2>';

    $data = $destiny->get_users_checked_out_books($idnumber);

    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo get_string('patron_column_heading', 'block_destiny'); ?></th>
                <th><?php echo get_string('item_title_column_heading', 'block_destiny'); ?></th>
                <th><?php echo get_string('call_number_column_heading', 'block_destiny'); ?>r</th>
                <th><?php echo get_string('due_date_column_heading', 'block_destiny'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($data as $item) {
            $item->due = strtotime($item->due);
            $overdue = $item->due < time();
            ?>
            <tr>
                <td><p><?php echo $item->patron_name; ?></p></td>
                <td><p><?php echo $item->title ?></p></td>
                <td><p><?php echo $item->call_number ?></p></td>
                <td><p <?php echo ($overdue ? 'class="text-danger"' : '')?>><?php echo date('l F jS Y', $item->due); ?></p></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
}

echo $OUTPUT->footer();
