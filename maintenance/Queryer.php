<?php

use CirrusSearch\Connection;
use ConfigFactory;

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once ( "$IP/maintenance/Maintenance.php" );

class Queryer extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Query Wikibase";
	}

	public function execute() {
		$path = 'wikidatawiki_content_first/_search';
		$data = array();

		$config = ConfigFactory::getDefaultInstance()->makeConfig( 'CirrusSearch' );
		$connection = new Connection( $config );

		$client = $connection->getClient();

		$result = $client->request(
			$path,
			Elastica\Request::GET,
			$data,
			array()
		);

		echo $result;
	}
}

$maintClass = "Queryer";
require_once RUN_MAINTENANCE_IF_MAIN;
