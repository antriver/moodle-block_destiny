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
 * Class to fetch data from Destiny database
 *
 * @package    block_destiny
 * @copyright  2015 Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_destiny\local;

use Exception;
use PDO;

/**
 * Class to fetch data from Destiny database
 *
 * @package    block_destiny
 * @copyright  2015 Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class destiny
{
    /**
     * @var PDO
     */
    private $db = null;

    /**
     * Connect to the Destiny database.
     * @return boolean
     */
    private function connect_to_database() {

        if ($this->db instanceof PDO) {
            return true;
        }

        $dbhost = get_config('block_destiny', 'db_host');
        $dbname = get_config('block_destiny', 'db_name');
        $dbuser = get_config('block_destiny', 'db_user');
        $dbpass = get_config('block_destiny', 'db_pass');

        $this->db = new PDO(
            "dblib:host={$dbhost};dbname={$dbname}",
            $dbuser,
            $dbpass
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        return true;
    }

    /**
     * Returns an array of idnumbers that should be looked up in Destiny.
     * It returs the idnumber of the current user and children.
     *
     * @param  boolean $includecurrentuser
     * @param  boolean $includechildren
     * @return array
     * @throws Exception
     */
    public function get_idnumbers($includecurrentuser = true, $includechildren = true) {

        global $USER;
        $idnumbers = array();

        // Include the current user.
        if ($includecurrentuser && $USER->idnumber) {
            $idnumbers[$USER->idnumber] = $USER->firstname . ' '. $USER->lastname;
        }

        // If the current user has children, include them too.
        if ($includechildren && $children = $this->get_users_children($USER->id)) {
            foreach ($children as $child) {
                $idnumbers[$child->idnumber] = $child->firstname . ' '. $child->lastname;
            }
        }

        // Require at least one idnumber.
        if (empty($idnumbers)) {
            throw new Exception(get_string('no_id_number', 'block_destiny'));
        }

        return $idnumbers;
    }

    /**
     * Returns user that the given user is a parent of.
     *
     * @param  int $userid
     * @return array
     */
    public function get_users_children($userid) {

        global $DB;
        $records = $DB->get_records_sql("
        SELECT
            c.instanceid,
            c.instanceid,
            u.id AS userid,
            u.firstname,
            u.lastname,
            u.idnumber
         FROM {role_assignments} ra, {context} c, {user} u
         WHERE ra.userid = ?
              AND ra.contextid = c.id
              AND c.instanceid = u.id
              AND c.contextlevel = " . \CONTEXT_USER, array($userid));
        return $records;
    }

    /**
     * Performs a SELECT query on the Destiny databaseand returns an array of the result objects
     *
     * @param  string $sql
     * @param  array  $params
     * @return array
     */
    private function select($sql, $params = array()) {

        $this->connect_to_database();
        $q = $this->db->prepare($sql);
        $q->execute($params);
        return $q->fetchAll();
    }

    /**
     * Returns the SQL select statement used to get a user's info from Destiny
     *
     * @return string
     */
    private function get_select_sql() {

        return "SELECT
            cpy.CopyID AS 'copy_id',
            pat.FirstName + ' ' + pat.LastName AS 'patron_name',
            pat.DistrictID AS 'patron_districtid',
            sitepat.PatronBarcode AS 'patron_barcode',
            cpy.DateDue AS 'due',
            cpy.CallNumber AS 'call_number',
            bibmstr.Title AS title
        FROM
            CircCatAdmin.Copy cpy
        JOIN
            CircCatAdmin.Patron pat ON pat.PatronID = cpy.PatronID
        JOIN
            CircCatAdmin.SitePatron sitepat ON sitepat.PatronID = cpy.PatronID
        LEFT JOIN
            CircCatAdmin.BibMaster bibmstr ON bibmstr.BibID = cpy.BibID
        WHERE
            cpy.dateOut IS NOT NULL
            AND
            cpy.dateReturned IS NULL
            AND
            cpy.DateLost IS NULL
        ";
    }

    /**
     * Returns all the checked out books for the user with the given district ID in Destiny
     *
     * @param  int $userdistrictid
     * @return array
     */
    public function get_users_checked_out_books($userdistrictid) {

        $sql = $this->get_select_sql();
        $sql .= 'AND pat.DistrictID = ?';
        $rows = $this->select($sql, array($userdistrictid));

        // Workaround to not have to use GROUP BY in query (MSSQL is weird about it).
        $results = [];
        foreach ($rows as $row) {
            $results[$row->copy_id] = $row;
        }

        return $results;
    }
}
