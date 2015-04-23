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

require_login();

// Create Destiny access object
$destiny = new \block_destiny\Destiny();


/**
 * Who are we going to show...
 */
$idNumbers = $destiny->getIdNumbers();

if (count($idNumbers) > 1) {
    $introText = get_string('multiple_intro_text', 'block_destiny');
} else {
    $introText = get_string('intro_text', 'block_destiny');
}

?>

<div class="alert alert-info">
    <i class="fa fa-book pull-left fa-2x"></i>
    <p><?php echo $introText; ?></p>
</div>

<br/>

<?php

foreach ($idNumbers as $idNumber => $name) {

    echo '<h2>' . $name . '</h2>';

    $data = $destiny->getUsersCheckedOutBooks($idNumber);

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
                <td><p <?=($overdue ? 'class="text-danger"' : '')?>><?php echo date('l F jS Y', $item->due) ?></p></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
}

echo $OUTPUT->footer();
