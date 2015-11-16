<?php

namespace Wikibase\Elastic\Hooks;

use Wikibase\Lib\ContentLanguages;
use Wikibase\Lib\WikibaseContentLanguages;

class SpecialSearchSyntaxHookHandler {

	/**
	 * @var ContentLanguages
	 */
	private $contentLanguages;

	public static function onCirrusSearchExtractSpecialSyntax(
		$syntaxKey,
		$searchText,
		&$filterDestination,
		&$searchContainedSyntax,
		&$match
	) {
		$handler = self::newFromGlobalState();
		$handler->extractSyntax(
			$syntaxKey,
			$searchText,
			$filterDestination,
			$searchContainedSyntax
		);

		return false;
	}

	private static function newFromGlobalState() {
		return new self(
			new WikibaseContentLanguages()
		);
	}

	public function __construct( ContentLanguages $contentLanguages ) {
		$this->contentLanguages = $contentLanguages;
	}

	public function extractSyntax(
		$syntaxKey,
		$searchText,
		array &$filterDestination,
		&$searchContainedSyntax
	) {
		$languageCodes = $this->contentLanguages->getLanguages();
		preg_match( '/label-([\w]+)/', $syntaxKey, $matches );

		if ( isset( $matches[1] ) && in_array( $matches[1], $languagesCodes ) ) {
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
	}

}
