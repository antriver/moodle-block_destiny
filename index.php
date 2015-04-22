<?php

/**
 * @package    block_destiny
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Load Moodle
require_once dirname(dirname(dirname(__FILE__))) .  '/config.php';

// Show page header
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/destiny');
$PAGE->set_title(get_string('page_title', 'block_destiny'));
$PAGE->set_heading(get_string('page_heading', 'block_destiny'));
echo $OUTPUT->header();

/**
 * Get user's idnumber
 */

// For easy testing, add ?idnumber=123 to the URL
// This could be a security risk so is normally disabled (with the false)
if (false && !empty($_GET['idnumber'])) {
    $idnumber = $_GET['idnumber'];
} else {
    require_login();
    $idnumber = $USER->idnumber;
}

// Require an ID number to continue
if (empty($idnumber)) {
    throw new \Exception(get_string('no_id_number', 'block_destiny'));
}

// Create Destiny access object
$destiny = new \block_destiny\Destiny();

// FIXME: SSIS specific
// Is the user a parent on a "normal" user?
if (strpos($idnumber, 'P') === 4) {
    $familyID = substr($idnumber, 0, 4);
    $introText = get_string('parent_intro_text', 'block_destiny');
    $data = $destiny->getFamilyCheckedOutBooks($familyID);
} else {
    $introText = get_string('intro_text', 'block_destiny');
    $data = $destiny->getUsersCheckedOutBooks($idnumber);
}

?>
<div class="alert alert-info">
    <i class="fa fa-book pull-left fa-2x"></i>
    <p><?php echo $introText; ?></p>
</div>

<br/>

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
            <td><p <?=($overdue ? 'class="text-danger"' : '')?>><?php echo date('l F jS Y', $item->due) ?></p></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

<?php
echo $OUTPUT->footer();
