<?php

namespace Wikibase\Elastic\Document;

use Content;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Term\Fingerprint;
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
			'sitelink_count' => 0
		);

		if ( $entity instanceof Item ) {
			$fields['sitelink_count'] = $entity->getSiteLinkList()->count();
		}

		return $fields;
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
