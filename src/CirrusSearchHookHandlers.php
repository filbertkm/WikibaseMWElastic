<?php

namespace Wikibase\Search\Elastic;

use CirrusSearch\Connection;
use CirrusSearch\Maintenance\MappingConfigBuilder;
use Content;
use Elastica\Document;
use ParserOutput;
use Title;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;
use Wikibase\Elastic\EntityIndexer;
use Wikibase\Elastic\Fields\WikibaseFieldDefinitions;
use Wikibase\EntityContent;
use Wikibase\Lib\MediaWikiContentLanguages;

class CirrusSearchHookHandlers {

	/**
	 * @var WikibaseFieldsDefinition
	 */
	private $fieldDefinitions;

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
		$hookHandler = self::newFromGlobalState();
		$hookHandler->indexExtraFields( $document, $content );

		return true;
	}

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
		$handler = self::newFromGlobalState();
		$handler->addExtraFields( $config );

		return true;
	}

	/**
	 * @return BuildDocumentParserHookHandler
	 */
	public static function newFromGlobalState() {
		$contentLanguages = new MediaWikiContentLanguages();

		$entitySearchFields = array(
			Item::ENTITY_TYPE => array( 'labels', 'descriptions' ),
			Property::ENTITY_TYPE => array( 'labels', 'descriptions' )
		);

		return new self(
			new WikibaseFieldDefinitions(
				$entitySearchFields,
				array( 'labels', 'descriptions' ),
				$contentLanguages->getLanguages()
			)
		);
	}

	/**
	 * @param WikibaseFieldDefinitions $fieldDefinitions
	 */
	public function __construct( WikibaseFieldDefinitions $fieldDefinitions ) {
		$this->fieldDefinitions = $fieldDefinitions;
	}

	/**
	 * @param Document $document
	 * @param Content $content
	 */
	public function indexExtraFields( Document $document, Content $content ) {
		if ( !$content instanceof EntityContent || $content->isRedirect() === true ) {
			return;
		}

		$entity = $content->getEntity();
		$fields = $this->fieldDefinitions->getFieldsForIndexing( $entity->getType() );

		$entityIndexer = new EntityIndexer( $fields );
		$entityIndexer->doIndex( $entity, $document );
	}

	/**
	 * @param array &$config
	 */
	public function addExtraFields( array &$config ) {
		$fields = $this->fieldDefinitions->getFieldsForMapping();

		foreach ( $fields as $fieldName => $field ) {
			$config['page']['properties'][$fieldName] = $field->getMapping();
		}
	}

}
