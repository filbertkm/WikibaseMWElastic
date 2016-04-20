<?php

namespace Wikibase\Search\Elastic;

use CirrusSearch\Connection;
use CirrusSearch\Maintenance\MappingConfigBuilder;
use Content;
use Elastica\Document;
use ParserOutput;
use Title;
use Wikibase\Elastic\EntityFieldsIndexer;
use Wikibase\Elastic\Fields\WikibaseFieldDefinitions;
use Wikibase\Elastic\Mapping\WikibaseMappingBuilder;
use Wikibase\EntityContent;
use Wikibase\Lib\MediaWikiContentLanguages;

class CirrusSearchHookHandlers {

	/**
	 * @var WikibaseMappingBuilder
	 */
	private $wikibaseMappingBuilder;

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
		$languageCodes = $contentLanguages->getLanguages();

		$wikibaseFieldDefinitions = new WikibaseFieldDefinitions( $languageCodes );
		$fields = $wikibaseFieldDefinitions->getSearchFieldDefinitions();

		$wikibaseMappingBuilder = new WikibaseMappingBuilder( $fields );
		$entityFieldsIndexer = new EntityFieldsIndexer( $fields, $languageCodes );

		return new self( $wikibaseMappingBuilder, $entityFieldsIndexer );
	}

	public function __construct(
		WikibaseMappingBuilder $wikibaseMappingBuilder,
		EntityFieldsIndexer $entityFieldsIndexer
	) {
		$this->wikibaseMappingBuilder = $wikibaseMappingBuilder;
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

		$document = $this->entityFieldsIndexer->doIndex( $content->getEntity(), $document );
	}

	/**
	 * @param array &$config
	 */
	public function addExtraFields( array &$config ) {
		$properties = $this->wikibaseMappingBuilder->getProperties();

		foreach ( $properties as $propertyName => $property ) {
			$config['page']['properties'][$propertyName] = $property;
		}
	}

}
