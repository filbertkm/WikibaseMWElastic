<?php

namespace Wikibase\Elastic\Tests\Hooks;

use Elastica\Document;
use ParserOutput;
use PHPUnit_Framework_TestCase;
use Title;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\Elastic\Fields\WikibaseFieldDefinitions;
use Wikibase\Elastic\Hooks\CirrusSearchHookHandlers;
use Wikibase\Repo\WikibaseRepo;

/**
 * @covers Wikibase\Elastic\Hooks\CirrusSearchHookHandlers
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

		$this->assertSame( 1, $document->get( 'sitelink_count' ), 'sitelink_count' );
		$this->assertSame( 1, $document->get( 'statement_count' ), 'statement_count' );
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

		$this->assertSame(
			array( 'labels', 'descriptions', 'sitelink_count', 'statement_count' ),
			array_keys( $config['page']['properties'] )
		);
	}

	public function testIndexExtraFields() {
		$fieldDefinitions = $this->newFieldDefinitions();

		$document = new Document();
		$content = $this->getContent();

		$hookHandlers = new CirrusSearchHookHandlers( $fieldDefinitions );
		$hookHandlers->indexExtraFields( $document, $content );

		$this->assertSame( 1, $document->get( 'sitelink_count' ), 'sitelink_count' );
		$this->assertSame( 1, $document->get( 'statement_count' ), 'statement_count' );
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
					'labels' => array(
						'type' => 'nested',
						'properties' => array(
							'label_ar' => array(
								'type' => 'string'
							),
							'label_es' => array(
								'type' => 'string'
							)
						)
					),
					'descriptions' => array(
						'type' => 'nested',
						'properties' => array(
							'description_ar' => array(
								'type' => 'string'
							),
							'description_es' => array(
								'type' => 'string'
							)
						)
					),
					'sitelink_count' => array(
						'type' => 'long'
					),
					'statement_count' => array(
						'type' => 'long'
					)
				)
			)
		);

		$this->assertSame( $expected, $config );
	}

	private function newFieldDefinitions() {
		$languageCodes = array( 'ar', 'es' );

		return new WikibaseFieldDefinitions( $languageCodes );
	}

	private function getContent() {
		$item = new Item();
		$item->getSiteLinkList()->addNewSiteLink( 'enwiki', 'Kitten' );
		$item->getStatements()->addNewStatement(
			new PropertyNoValueSnak( new PropertyId( 'P1' ) )
		);

		$entityContentFactory = WikibaseRepo::getDefaultInstance()->getEntityContentFactory();

		return $entityContentFactory->newFromEntity( $item );
	}

}
