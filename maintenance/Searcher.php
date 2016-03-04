<?php

namespace Wikibase\Search\Elastic\Maintenance;

use CirrusSearch\Connection;
use ConfigFactory;
use Maintenance;
use SearchResultSet;
use Status;
use Wikibase\Search\Elastic\Search\SearchExecutor;
use Wikibase\Search\Elastic\Search\SearchResultPrinter;

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../../..';
}

require_once ( "$IP/maintenance/Maintenance.php" );

/**
 * @license GPL 2.0+
 */
class Searcher extends Maintenance {

	public function __construct() {
		parent::__construct();

		$this->mDescription = "Query Wikibase";
		$this->addArg( 'search', 'Search text' );
	}

	public function execute() {
		$config = ConfigFactory::getDefaultInstance()->makeConfig( 'CirrusSearch' );
		$connection = new Connection( $config );

		$searchExecutor = new SearchExecutor( $connection, $config, wfWikiId() );
		$results = $searchExecutor->execute( $this->getArg() );

		if ( $results instanceof SearchResultSet ) {
			$printer = new SearchResultPrinter();
			$printer->print( $results );
		} else if ( $results instanceof Status ) {
			echo $results->getMessage()->text() . "\n";
		} else {
			echo "no results found\n";
		}
	}

}

$maintClass = "Wikibase\Search\Elastic\Maintenance\Searcher";
require_once RUN_MAINTENANCE_IF_MAIN;
