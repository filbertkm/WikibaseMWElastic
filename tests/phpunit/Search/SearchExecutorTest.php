<?php

namespace Wikibase\Search\Elastic\Tests;

use CirrusSearch\SearchConfig;
use PHPUnit_Framework_TestCase;
use Wikibase\Search\Elastic\Search\SearchExecutor;

/**
 * @covers Wikibase\Search\Elastic\Search\SearchExecutor
 *
 * @licence GNU 2.0+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class SearchExecutorTest extends PHPUnit_Framework_TestCase {

	public function testExecute() {
		$config = new SearchConfig();
		$executor = new SearchExecutor( $config );

		$results = $executor->execute( 'life', 3 );
		$this->assertInstanceOf( 'Status', $results );

		$this->assertEquals( 0, $results->numRows() );
	}

}
