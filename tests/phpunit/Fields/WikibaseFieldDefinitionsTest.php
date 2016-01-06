<?php

namespace Wikibase\Elastic\Tests\Fields;

use PHPUnit_Framework_TestCase;
use Wikibase\Elastic\Fields\WikibaseFieldDefinitions;
use Wikibase\Lib\MediaWikiContentLanguages;

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
			$this->getLanguageCodes()
		);

		$fields = $wikibaseFieldDefinitions->getFields();

		$expectedFieldNames = array( 'labels' );

		$this->assertSame( $expectedFieldNames, array_keys( $fields ) );
	}

	public function testGetFields_instanceOfSearchIndexField() {
		$wikibaseFieldDefinitions = new WikibaseFieldDefinitions(
			array( 'labels' ),
			$this->getLanguageCodes()
		);

		foreach ( $wikibaseFieldDefinitions->getFields() as $fieldName => $field ) {
			$this->assertInstanceOf(
				'Wikibase\Elastic\Fields\Field',
				$field,
				"$fieldName must be instance of Field"
			);
		}
	}

	private function getLanguageCodes() {
		$contentLanguages = new MediaWikiContentLanguages();

		return $contentLanguages->getLanguages();
	}

}
