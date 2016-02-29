<?php

namespace Wikibase\Search\Elastic\Maintenance;

use ConfigFactory;
use Maintenance;
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
		$configFactory = ConfigFactory::getDefaultInstance();

		$searchExecutor = new SearchExecutor( $configFactory );
		$results = $searchExecutor->execute( $this->getArg() );

		$printer = new SearchResultPrinter();
		$printer->print( $results );
	}

}

$maintClass = "Wikibase\Search\Elastic\Maintenance\Searcher";
require_once RUN_MAINTENANCE_IF_MAIN;
