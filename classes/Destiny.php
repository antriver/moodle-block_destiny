<?php

/**
 * @package    block_destiny
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class to fetch data from Destiny database
 */

namespace block_destiny;

use Exception;
use PDO;

class Destiny
{
	private $db = null;

	function __construct()
	{

	}

	/**
	 * Connect to database and return a PDO object
	 */
	private function connectToDb()
	{
        if ($this->db instanceof PDO) {
            return true;
        }

        $dbHost = get_config('block_destiny', 'db_host');
        $dbName = get_config('block_destiny', 'db_name');
        $dbUser = get_config('block_destiny', 'db_user');
        $dbPass = get_config('block_destiny', 'db_pass');

		$this->db = new PDO(
			"dblib:host={$dbHost};dbname={$dbName}",
			$dbUser,
			$dbPass
		);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	}

    /**
     * Returns an array of idnumbers that will be looked up in Destiny
     * @param  boolean $includeCurrentUser
     * @param  boolean $includeChildren
     * @return array
     * @throws Exception
     */
    public function getIdNumbers($includeCurrentUser = true, $includeChildren = true)
    {
        global $USER;
        $idNumbers = array();

        // Include the current user
        if ($includeCurrentUser && $USER->idnumber) {
            $idNumbers[$USER->idnumber] = $USER->firstname . ' '. $USER->lastname;
        }

        // If the current user has children, include them too
        if ($includeChildren && $children = $this->getUsersChildren($USER->id)) {
            foreach ($children as $child) {
                $idNumbers[$child->idnumber] = $child->firstname . ' '. $child->lastname;
            }
        }

        // Require at least one idnumber
        if (empty($idNumbers)) {
            throw new Exception(get_string('no_id_number', 'block_destiny'));
        }

        return $idNumbers;
    }

    public function getUsersChildren($userId)
    {
        global $DB;
        $usercontexts = $DB->get_records_sql("
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
              AND c.contextlevel = " . \CONTEXT_USER, array($userId));
        return $usercontexts;
    }

	/**
	 * Performs a SELECT query and returns an array of the result objects
	 */
	private function select($sql, $params = array())
	{
        $this->connectToDb();
		$q = $this->db->prepare($sql);
		$q->execute($params);
		return $q->fetchAll();
	}

	/**
	 * Returns the SQL select statement used to get a user's info from Destiny
	 */
	private function getSelectQuery()
	{
		return "SELECT
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

	public function getUsersCheckedOutBooks($userDistrictID)
	{
		$sql = $this->getSelectQuery();
		$sql .= 'AND pat.DistrictID = ?';
		return $this->select($sql, array($userDistrictID));
	}
}
