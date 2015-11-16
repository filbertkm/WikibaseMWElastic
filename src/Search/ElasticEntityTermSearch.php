<?php

namespace Wikibase\Elastic\Search;

use CirrusSearch\Connection;
use Elastica\Type;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;
use Wikibase\Lib\Store\EntityTermSearch;
use Wikibase\TermIndexEntry;

/**
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class ElasticEntityTermSearch implements EntityTermSearch {

	/**
	 * @var Type
	 */
	private $elasticType;

	public function __construct( Connection $connection, $indexBaseName ) {
		$this->elasticType = $connection->getPageType(
			$indexBaseName,
			Connection::CONTENT_INDEX_TYPE
		);
	}

	/**
	 * Returns the terms that match the provided conditions ranked with
	 * the 'most important' / top first.
	 *
	 * Will only return one TermIndexEntry per Entity.
	 *
	 * $terms is an array of Term objects. Terms are joined by OR.
	 * The fields of the terms are joined by AND.
	 *
	 * A default can be provided for termType and entityType via the corresponding
	 * method parameters.
	 *
	 * The return value is an array of Terms where entityId, entityType,
	 * termType, termLanguage, termText, termWeight are all set.
	 *
	 * @param TermIndexEntry[] $terms
	 * @param string|string[]|null $termType
	 * @param string|string[]|null $entityType
	 * @param array $options
	 *		Accepted options are:
	 *		- caseSensitive: boolean, default true
	 *		- prefixSearch: boolean, default false
	 *		- LIMIT: int, defaults to none
	 *
	 * @return TermIndexEntry[]
	 */
	public function getTopMatchingTerms(
		array $terms,
		$termType = null,
		$entityType = null,
		array $options = array()
	) {
		foreach ( $terms as $term ) {
			return $this->search( $term );
		}
	}

	private function search( TermIndexEntry $termIndexEntry ) {
		$query = $this->buildQuery( $termIndexEntry );
		$search = $this->elasticType->createSearch( $query );

		$results = $search->search();
		$entries = array();

		foreach ( $search->search() as $result ) {
			$entries[] = $this->getTermIndexEntryFromResult(
				$result,
				$termIndexEntry->getLanguage()
			);
		}

		return $entries;
	}

	private function buildQuery( TermIndexEntry $termIndexEntry ) {
		$prefixQuery = new \Elastica\Query\Prefix();
		$prefixQuery->setPrefix(
			$termIndexEntry->getLanguage(),
			$termIndexEntry->getText()
		);

		$nested = new \Elastica\Filter\Nested();
		$nested->setPath( 'labels' );
		$nested->setFilter(
			new \Elastica\Filter\Query( $prefixQuery )
		);

		$matchAllQuery = new \Elastica\Query\MatchAll();

		return new \Elastica\Query\Filtered( $matchAllQuery, $nested );
	}

	private function getTermIndexEntryFromResult( $result, $languageCode ) {
		$data = $result->getData();

		$label = $data['labels'][$languageCode];
		$entityId = new ItemId( $data['title'] );

		return new TermIndexEntry( array(
			'entityId' => $entityId->getNumericId(),
			'entityType' => 'item',
			'termText' => $label,
			'termLanguage' => $languageCode,
			'termType' => 'label'
		) );
	}

}
