<?php

namespace Wikibase\Elastic\Hooks;

use CirrusSearch\Connection;
use Content;
use Elastica\Document;
use ParserOutput;
use Title;
use Wikibase\Elastic\Document\WikibaseFieldsIndexer;
use Wikibase\EntityContent;
use Wikibase\Lib\WikibaseContentLanguages;

/**
 * Extension hooks
 */
class BuildDocumentParseHookHandler {

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
	 * @return BuildDocumentParserHookHandler
	 */
	public static function newFromGlobalState() {
		return new self(
			new WikibaseFieldsIndexer()
		);
	}

	/**
	 * @param WikibaseFieldsIndexer $fieldsIndexer
	 */
	public function __construct( WikibaseFieldsIndexer $fieldsIndexer ) {
		$this->fieldsIndexer = $fieldsIndexer;
	}

	/**
	 * @param Document $document
	 * @param Content $content
	 */
	public function indexExtraFields( Document $document, Content $content ) {
		if ( !$content instanceof EntityContent ) {
			return;
		}

		$properties = $this->fieldsIndexer->build( $content );

		foreach ( $properties as $property => $data ) {
			$document->set( $property, $data );
		}
	}

}
