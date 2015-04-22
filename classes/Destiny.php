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

use \PDO;

class Destiny
{
	private $config;
	private $db = null;

	function __construct()
	{
		$this->connectToDb();
	}

	/**
	 * Connect to database and return a PDO object
	 */
	private function connectToDb()
	{
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
	 * Performs a SELECT query and returns an array of the result objects
	 */
	private function select($sql, $params = array())
	{
		$q = $this->db->prepare($sql);
		$q->execute($params);
		return $q->fetchAll();
	}

	/**
	 * Returns the SQL select statement used to get a user's info from Destiny
	 */
	private function getSQL()
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
		$sql = $this->getSQL();
		$sql .= 'AND pat.DistrictID = ?';
		return $this->select($sql, array($userDistrictID));
	}

	public function getFamilyCheckedOutBooks($familyID)
	{
		$sql = $this->getSQL();
		$sql .= "AND pat.DistrictID LIKE ?";
		$familyID .= '%';
		return $this->select($sql, array($familyID));
	}

	public function dumpCheckedOutBooks()
	{
		/*$select = "
			cpy.CopyID,
			cpy.BibID,
			cpy.CopyBarcode,
			cpy.CallNumber,
			bibmstr.Title,
			cpy.DateOut,
			cpy.DateDue,
			cpy.DateReturned,
			pat.PatronID,
			pat.FirstName,
			pat.LastName,
			pat.UserID,
			pat.DistrictID,
			pat.EmailAddress1
		";*/

		$select = "
			pat.FirstName + ' ' + pat.LastName AS 'patron_name',
			pat.DistrictID AS 'patron_districtid',
			sitepat.PatronBarcode AS 'patron_barcode',
			cpy.DateDue AS 'due',
			cpy.CallNumber AS 'call_number',
			bibmstr.Title AS title
		";

		$sql = "SELECT
			{$select}
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
			cpy.dateReturned IS NULL";

		return $this->select($sql);
	}
}
