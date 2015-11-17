<?php

namespace Wikibase\Elastic;

use CirrusSearch\Connection;
use CirrusSearch\Maintenance\MappingConfigBuilder;
use Content;
use Elastica\Document;
use ParserOutput;
use Title;
use Wikibase\Elastic\Document\WikibaseFieldsIndexer;
use Wikibase\Elastic\Mapping\WikibaseFieldsBuilder;
use Wikibase\EntityContent;
use Wikibase\Lib\WikibaseContentLanguages;

/**
 * Extension hooks
 */
class Hooks {

	/**
	 * @param array &$config
	 * @param MappingConfigBuilder $mappingConfigBuilder
	 *
	 * @return bool
	 */
	public static function onCirrusSearchMappingConfig(
		array &$config,
		MappingConfigBuilder $mappingConfigBuilder
	) {
		// @todo index entity type? (e.g. 'item' or 'property')
		//
		// We do have namespace so this might be redundant if we always
		// have a 1:1 relation between entity type and namespace, but
		// don't think we should make that assumption.

		$wikibaseContentLanguages = new WikibaseContentLanguages();
		$languageCodes = $wikibaseContentLanguages->getLanguages();

		$fieldsBuilder = new WikibaseFieldsBuilder( $languageCodes );
		$fields = $fieldsBuilder->getFields();

		foreach ( $fields as $property => $propertyFields ) {
			$config['page']['properties'][$property] = $propertyFields;
		}

		return true;
	}

	/**
	 * @param Document $document
	 * @param Title $title
	 * @param Content $content
	 * @param ParserOutput $parserOutput
	 * @param Connection $connection
	 *
	 * @return bool
	 */
	public static function onCirrusSearchBuildDocumentParse(
		Document $document,
		Title $title,
		Content $content,
		ParserOutput $parserOutput,
		Connection $connection
	) {
		$fieldsIndexer = new WikibaseFieldsIndexer();
		$properties = $fieldsIndexer->build( $content );

		foreach ( $properties as $property => $data ) {
			$document->set( $property, $data );
		}

		return true;
	}

	public static function onWikibaseTextForSearchIndex( EntityContent $content, &$text ) {
		$text = '';
	}

}
