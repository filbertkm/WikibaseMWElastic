<?php

namespace Wikibase\Elastic\Tests\Fields;

use PHPUnit_Framework_TestCase;
use Wikibase\Elastic\Fields\WikibaseFieldDefinitions;

/**
 * @covers Wikibase\Elastic\Fields\WikibaseFieldDefinitions
 *
 * @group WikibaseElastic
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class WikibaseFieldDefinitionsTest extends PHPUnit_Framework_TestCase {

	public function testGetFields() {
		$wikibaseFieldDefinitions = new WikibaseFieldDefinitions(
			array( 'labels' ),
			array( 'ar', 'en', 'es' )
		);

		$fields = $wikibaseFieldDefinitions->getFields();

		$expectedFieldNames = array( 'label_ar', 'label_en', 'label_es' );

		$this->assertSame( $expectedFieldNames, array_keys( $fields ) );
	}

	public function testGetFields_instanceOfSearchIndexField() {
		$wikibaseFieldDefinitions = new WikibaseFieldDefinitions(
			array( 'labels' ),
			array( 'de', 'es', 'ja' )
		);

		foreach ( $wikibaseFieldDefinitions->getFields() as $fieldName => $field ) {
			$this->assertInstanceOf(
				'Wikibase\Elastic\Fields\Field',
				$field,
				"$fieldName must be instance of Field"
			);
		}
	}

	public function testConstruct_withInvalidType() {
		$this->setExpectedException( 'InvalidArgumentException' );

		$wikibaseFieldDefinitions = new WikibaseFieldDefinitions(
			array( 'kittens' ),
			array( 'de', 'es', 'fr' )
		);
	}

}
