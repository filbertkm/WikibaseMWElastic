<?php

namespace Wikibase\Elastic;

use CirrusSearch\Connection;
use CirrusSearch\Maintenance\MappingConfigBuilder;
use Content;
use Elastica\Document;
use ParserOutput;
use Title;
use Wikibase\Elastic\Document\DocumentTermsBuilder;
use Wikibase\Elastic\Mapping\TermMappingBuilder;
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
		$wikibaseContentLanguages = new WikibaseContentLanguages();
		$languageCodes = $wikibaseContentLanguages->getLanguages();

		$termMappingBuilder = new TermMappingBuilder( $languageCodes );
		$properties = $termMappingBuilder->getProperties();

		foreach ( $properties as $property => $fields ) {
			$config['page']['properties'][$property] = $fields;
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
		$documentTermsBuilder = new DocumentTermsBuilder();
		$properties = $documentTermsBuilder->build( $content );

		foreach ( $properties as $property => $data ) {
			$document->set( $property, $data );
		}

		return true;
	}

	public static function onCirrusSearchExtractSpecialSyntax(
		$key,
		$searchText,
		&$filterDestination,
		&$searchContainedSyntax,
		&$match
	) {
		$languageCodes = array( 'de', 'en', 'es' );
		$keys = array_map( function( $key ) {
				return "label-$key";
			}, $languageCodes
		);

		preg_match( '/label-([\w]+)/', $key, $matches );

		if ( isset( $matches[1] ) ) {
			$languageCode = $matches[1];
			$searchContainedSyntax = true;

			$nested = new \Elastica\Filter\Nested();
			$nested->setPath( 'labels' );
			$nested->setFilter(
				new \Elastica\Filter\Query(
					new \Elastica\Query\Match( $languageCode, $searchText )
				)
			);

			$filterDestination[] = $nested;
		}

		return false;
	}

	public static function onWikibaseTextForSearchIndex( EntityContent $content, &$text ) {
		$text = '';
	}

}
