<?php

namespace Wikibase\Search\Elastic\Search;

use CirrusSearch;
use CirrusSearch\Connection;
use CirrusSearch\Search\ResultSet;
use Config;
use SearchResultSet;
use Status;

/**
 * @license GPL 2.0+
 */
class SearchExecutor {

	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var string
	 */
	private $indexBase;

	/**
	 * @var int
	 */
	private $limit;

	/**
	 * @param Connection $connection
	 * @param Config $config
	 * @param string $indexBase
	 * @param int $limit
	 */
	public function __construct( Connection $connection, Config $config, $indexBase, $limit = 10 ) {
		$this->connection = $connection;
		$this->config = $config;
		$this->indexBase = $indexBase;
		$this->limit = $limit;
	}

	/**
	 * @param string $searchText
	 *
	 * @return SearchResultSet|Status
	 */
	public function execute( $searchText ) {
		$engine = new CirrusSearch( $this->indexBase );

		$engine->setLimitOffset( $this->limit );
		$engine->setConnection( $this->connection );
		$engine->setConfig( $this->config );

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
