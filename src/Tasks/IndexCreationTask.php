<?php

namespace Wikibase\Search\Elastic\Tasks;

use CirrusSearch\Connection;
use CirrusSearch\Maintenance\AnalysisConfigBuilder;
use CirrusSearch\Maintenance\IndexCreator;
use CirrusSearch\SearchConfig;

/**
 * @license GPL 2.0+
 */
class IndexCreationTask {

	/**
	 * @var SearchConfig
	 */
	private $config;

	/**
	 * @param SearchConfig $config
	 */
	public function __construct( SearchConfig $config ) {
		$this->config = $config;
	}

	public function execute( $indexName ) {
        $connection = Connection::getPool( $this->config );
        $index = $connection->getClient()->getIndex( $indexName );

        $indexCreator = new IndexCreator( $index, new AnalysisConfigBuilder( 'en', array() ) );
        $status = $indexCreator->createIndex( true, 'unlimited', 4, '0-2', 30, array(), true );

		if ( $status->isOK() !== true ) {
			echo $status->getMessage()->text() . "\n";
		} else {
			echo "done \n";
		}
	}

}
