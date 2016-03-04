<?php

namespace Wikibase\Search\Elastic\Maintenance;

use CirrusSearch\SearchConfig;
use Maintenance;
use Wikibase\Search\Elastic\Tasks\IndexCreationTask;

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../../..';
}

require_once ( "$IP/maintenance/Maintenance.php" );

/**
 * @license GPL 2.0+
 */
class CreateIndex extends Maintenance {

	public function __construct() {
		parent::__construct();

		$this->mDescription = "Create index";
		$this->addArg( 'index-name', 'Index name' );
	}

	public function execute() {
		$indexName = $this->getArg();
		$config = new SearchConfig( $indexName );

		$indexCreationTask = new IndexCreationTask( $config );
		$indexCreationTask->execute( $indexName );
	}

}

$maintClass = "Wikibase\Search\Elastic\Maintenance\CreateIndex";
require_once RUN_MAINTENANCE_IF_MAIN;
