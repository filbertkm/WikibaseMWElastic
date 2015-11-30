<?php

namespace Wikibase\Elastic\Document;

use Content;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Term\Fingerprint;
use Wikibase\DataModel\Statement\StatementListHolder;
use Wikibase\DataModel\Term\TermList;
use Wikibase\EntityContent;

class WikibaseFieldsIndexer {

	/**
	 * @param Content $content
	 *
	 * @return array
	 */
	public function build( Content $content ) {
		if ( !$content instanceof EntityContent || $content->isRedirect() === true ) {
			return array();
		}

		$entity = $content->getEntity();
		$terms = $entity->getFingerprint();

		$fields = array(
			'labels' => $this->indexLabels( $terms ),
			'descriptions' => $this->indexDescriptions( $terms ),
			'entity_type' => $entity->getType(),
			'sitelink_count' => $this->getSiteLinkCount( $entity ),
			'statement_count' => $this->getStatementCount( $entity )
		);

		return $fields;
	}

	private function getSiteLinkCount( Entity $entity ) {
		if ( $entity instanceof Item ) {
			return $entity->getSiteLinkList()->count();
		}

		return 0;
	}

	private function getStatementCount( Entity $entity ) {
		if ( $entity instanceof StatementListHolder ) {
			return $entity->getStatements()->count();
		}

		return 0;
	}

	/**
	 * @param Fingerprint $terms
	 *
	 * @return array
	 */
	private function indexLabels( Fingerprint $terms ) {
		return $this->buildTermsData( $terms->getLabels(), 'label' );
	}

	/**
	 * @param Fingerprint $terms
	 *
	 * @return array
	 */
	private function indexDescriptions( Fingerprint $terms ) {
		return $this->buildTermsData( $terms->getDescriptions(), 'description' );
	}

	/**
	 * @param TermList $terms
	 *
	 * @return array
	 */
	private function buildTermsData( $terms, $prefix ) {
		$termsArray = array();

		foreach ( $terms->toTextArray() as $languageCode => $term ) {
			$termsArray[$prefix . '_' . $languageCode] = $term;
		}

		return $termsArray;
	}

}
