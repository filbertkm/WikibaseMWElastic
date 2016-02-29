<?php

namespace Wikibase\Search\Elastic\Tests;

use Elastica\Document;
use ParserOutput;
use PHPUnit_Framework_TestCase;
use Title;
use Wikibase\DataModel\Entity\Item;
use Wikibase\Elastic\Fields\WikibaseFieldDefinitions;
use Wikibase\Search\Elastic\CirrusSearchHookHandlers;
use Wikibase\Repo\WikibaseRepo;

/**
 * @covers Wikibase\Search\Elastic\CirrusSearchHookHandlers
 *
 * @since 0.5
 *
 * @group WikibaseRepo
 * @group Wikibase
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class CirrusSearchHookHandlersTest extends PHPUnit_Framework_TestCase {

	public function testOnCirrusSearchBuildDocumentParse() {
		if ( !class_exists( 'CirrusSearch' ) ) {
			$this->markTestSkipped( 'CirrusSearch is not available' );
		}

		$connection = $this->getMockBuilder( 'CirrusSearch\Connection' )
			->disableOriginalConstructor()
			->getMock();

		$document = new Document();

		CirrusSearchHookHandlers::onCirrusSearchBuildDocumentParse(
			$document,
			Title::newFromText( 'Q1' ),
			$this->getContent(),
			new ParserOutput(),
			$connection
		);

		$this->assertSame(
			'kitten',
			$document->get( 'label_en' ),
			'en label'
		);

		$this->assertSame(
			'young cat',
			$document->get( 'description_en' ),
			'en description'
		);
	}

	public function testOnCirrusSearchMappingConfig() {
		if ( !class_exists( 'CirrusSearch' ) ) {
			$this->markTestSkipped( 'CirrusSearch is not available' );
		}

		$mappingConfigBuilder = $this->getMockBuilder(
				'CirrusSearch\Maintenance\MappingConfigBuilder'
			)
			->disableOriginalConstructor()
			->getMock();

		$config = array();

		CirrusSearchHookHandlers::onCirrusSearchMappingConfig( $config, $mappingConfigBuilder );

		$this->assertInternalType(
			'array',
			array_keys( $config['page']['properties'] )
		);
	}

	public function testIndexExtraFields() {
		$fieldDefinitions = $this->newFieldDefinitions();

		$document = new Document();
		$content = $this->getContent();

		$hookHandlers = new CirrusSearchHookHandlers( $fieldDefinitions );
		$hookHandlers->indexExtraFields( $document, $content );

		$this->assertEquals(
			'kitten',
			$document->get( 'label_en' ),
			'en label'
		);

		$this->assertEquals(
			'young cat',
			$document->get( 'description_en' ),
			'en description'
		);
	}

	public function testAddExtraFields() {
		$fieldDefinitions = $this->newFieldDefinitions();

		$document = new Document();
		$content = $this->getContent();

		$config = array();

		$hookHandlers = new CirrusSearchHookHandlers( $fieldDefinitions );
		$hookHandlers->addExtraFields( $config );

		$expected = array(
			'page' => array(
				'properties' => array(
					'label_ar' => array(
						'type' => 'string',
						'copy_to' => array( 'all', 'all_near_match' )
					),
					'label_en' => array(
						'type' => 'string',
						'copy_to' => array( 'all', 'all_near_match' )
					),
					'label_es' => array(
						'type' => 'string',
						'copy_to' => array( 'all', 'all_near_match' )
					),
					'description_ar' => array(
						'type' => 'string',
						'copy_to' => array( 'all' )
					),
					'description_en' => array(
						'type' => 'string',
						'copy_to' => array( 'all' )
					),
					'description_es' => array(
						'type' => 'string',
						'copy_to' => array( 'all' )
					)
				)
			)
		);

		$this->assertSame( $expected, $config );
	}

	private function newFieldDefinitions() {
		$languageCodes = array( 'ar', 'en', 'es' );

		return new WikibaseFieldDefinitions( array( 'labels', 'descriptions' ), $languageCodes );
	}

	private function getContent() {
		$item = new Item();

		$item->setLabel( 'en', 'kitten' );
		$item->setDescription( 'en', 'young cat' );

		$entityContentFactory = WikibaseRepo::getDefaultInstance()->getEntityContentFactory();

		return $entityContentFactory->newFromEntity( $item );
	}

}
