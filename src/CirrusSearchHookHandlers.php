<?php

namespace Wikibase\Search\Elastic;

use CirrusSearch\Connection;
use CirrusSearch\Maintenance\MappingConfigBuilder;
use Content;
use Elastica\Document;
use ParserOutput;
use Title;
use Wikibase\Elastic\FieldDefinitions\DispatchingFieldDefinitions;
use Wikibase\Elastic\FieldDefinitions\FieldDefinitions;
use Wikibase\Elastic\FieldDefinitions\ItemFieldDefinitions;
use Wikibase\Elastic\FieldDefinitions\PropertyFieldDefinitions;
use Wikibase\Elastic\Index\EntityFieldsIndexer;
use Wikibase\EntityContent;
use Wikibase\Lib\MediaWikiContentLanguages;

class CirrusSearchHookHandlers {

	/**
	 * @var FieldDefinitions
	 */
	private $fieldDefinitions;

	/**
	 * @var EntityFieldsIndexer
	 */
	private $entityFieldsIndexer;

	/**
	 * @var string[]
	 */
	private $languageCodes;

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
		static $instance;

		if ( !isset( $instance ) ) {
			$instance = self::newFromGlobalState();
		}

		$instance->indexExtraFields( $document, $content );

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
	 * @return self
	 */
	public static function newFromGlobalState() {
		$contentLanguages = new MediaWikiContentLanguages();
		$languageCodes = $contentLanguages->getLanguages();

		$itemFieldDefinitions = new ItemFieldDefinitions( $languageCodes );
		$propertyFieldDefinitions = new PropertyFieldDefinitions( $languageCodes );

		$fieldDefinitions = new DispatchingFieldDefinitions( [
			$itemFieldDefinitions,
			$propertyFieldDefinitions
		] );

		$entityFieldsIndexer = new EntityFieldsIndexer(
			$itemFieldDefinitions,
			$propertyFieldDefinitions
		);

		return new self( $fieldDefinitions, $entityFieldsIndexer );
	}

	public function __construct(
		FieldDefinitions $fieldDefinitions,
		EntityFieldsIndexer $entityFieldsIndexer
	) {
		$this->fieldDefinitions = $fieldDefinitions;
		$this->entityFieldsIndexer = $entityFieldsIndexer;
	}

	/**
	 * @param Document $document
	 * @param Content $content
	 */
	public function indexExtraFields( Document $document, Content $content ) {
		if ( !$content instanceof EntityContent || $content->isRedirect() === true ) {
			return;
		}

		$this->entityFieldsIndexer->doIndex( $content->getEntity(), $document );
	}

	/**
	 * @param array &$config
	 */
	public function addExtraFields( array &$config ) {
		$properties = $this->fieldDefinitions->getMappingProperties();

		foreach ( $properties as $propertyName => $property ) {
			$config['page']['properties'][$propertyName] = $property;
		}
	}

}
