<?php

namespace Wikibase\Search\Elastic\Search;

use CirrusSearch;
use CirrusSearch\Connection;
use CirrusSearch\Search\ResultSet;
use ConfigFactory;

/**
 * @license GPL 2.0+
 */
class SearchExecutor {

	private $configFactory;

	/**
	 * @param ConfigFactory $configFactory
	 */
	public function __construct( ConfigFactory $configFactory ) {
		$this->configFactory = $configFactory;
	}

	public function execute( $searchText ) {
        $config = $this->configFactory->makeConfig( 'CirrusSearch' );
        $connection = new Connection( $config );

        $engine = new CirrusSearch( wfWikiId() );
        $engine->setConnection( $connection );
        $engine->setLimitOffset( 10 );

		return $engine->searchText( $searchText );
    }

    private function printResults( ResultSet $results ) {
        $result = $results->next();

        while ( $result ) {
            $this->output( $result->getTitle()->getPrefixedText() . "\n" );
            $result = $results->next();
        }
    }

}
