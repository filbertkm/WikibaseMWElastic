<?php

namespace Wikibase\Search\Elastic\Tests;

use CirrusSearch\Connection;
use CirrusSearch\Maintenance\AnalysisConfigBuilder;
use CirrusSearch\Maintenance\IndexCreator;
use CirrusSearch\Maintenance\MappingConfigBuilder;
use CirrusSearch\SearchConfig;
use Elastica\Type\Mapping;
use PHPUnit_Framework_TestCase;
use Status;
use Wikibase\Search\Elastic\Search\SearchExecutor;

/**
 * @covers Wikibase\Search\Elastic\Search\SearchExecutor
 *
 * @licence GNU 2.0+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class SearchExecutorTest extends PHPUnit_Framework_TestCase {

	const INDEX_NAME = 'searchexecutortest';

	protected function setUp() {
		parent::setUp();

		$this->createTestIndex();
	}

	protected function tearDown() {
		parent::tearDown();

		$this->getIndex()->delete();
	}

	public function testExecute() {
		$executor = new SearchExecutor(
			$this->getConnection(),
			$this->getConfig(),
			self::INDEX_NAME
		);

		$results = $executor->execute( 'life', 3 );

		if ( $results instanceof Status ) {
			echo $results->getMessage()->text();
		}

		$this->assertInstanceOf( 'SearchResultSet', $results );
		$this->assertEquals( 0, $results->numRows() );
	}

	public function testExecute_missingIndex() {
		$executor = new SearchExecutor(
			$this->getConnection(),
			$this->getConfig(),
			uniqid( self::INDEX_NAME . '_' )
		);

		$results = $executor->execute( 'life', 3 );

		$this->assertInstanceOf( 'Status', $results );
	}

	private function createTestIndex() {
		$index = $this->getIndex();

		$indexCreator = new IndexCreator( $index, new AnalysisConfigBuilder( 'en', array() ) );
		$indexCreator->createIndex( true, 'unlimited', 4, '0-2', 30, array(), true );

		$mappingConfigBuilder = new MappingConfigBuilder( false );
		$mappingParams = $mappingConfigBuilder->buildConfig( 0 );

		$type = $index->getType( Connection::PAGE_TYPE_NAME );
		$mapping = new Mapping( $type );

		foreach ( $mappingParams['page'] as $key => $param ) {
			$mapping->setParam( $key, $param );
		}

		$mapping->send();
	}

	private function getConnection() {
		return Connection::getPool( $this->getConfig() );
	}

	private function getIndex() {
		return $this->getConnection()->getIndex(
			self::INDEX_NAME,
			Connection::CONTENT_INDEX_TYPE
		);
	}

	private function getConfig() {
		return new SearchConfig( self::INDEX_NAME );
	}

}
