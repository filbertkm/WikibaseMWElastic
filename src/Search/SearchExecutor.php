<?php

namespace Wikibase\Search\Elastic\Search;

use CirrusSearch;
use CirrusSearch\Connection;
use CirrusSearch\Search\ResultSet;
use Config;

/**
 * @license GPL 2.0+
 */
class SearchExecutor {

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var int
	 */
	private $limit;

	/**
	 * @param Config $config
	 * @param int $limit
	 */
	public function __construct( Config $config, $limit = 10 ) {
		$this->config = $config;
		$this->limit = $limit;
	}

	public function execute( $searchText ) {
        $connection = new Connection( $this->config );

        $engine = new CirrusSearch( wfWikiId() );
        $engine->setConnection( $connection );
        $engine->setLimitOffset( $this->limit );

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
