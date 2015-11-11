<?php

namespace Wikibase\Elastic\Document;

use Content;
use Wikibase\DataModel\Term\Fingerprint;
use Wikibase\EntityContent;

class DocumentTermsBuilder {

	/**
	 * @param Content $content
	 *
	 * @return array
	 */
	public function build( Content $content ) {
		if ( !$content instanceof EntityContent || $content->isRedirect() === true ) {
			return array();
		}

		$terms = $content->getEntity()->getFingerprint();

		return array(
			'labels' => $this->buildLabelsData( $terms )
		);
	}

	/**
	 * @param Fingerprint $terms
	 *
	 * @return array
	 */
	private function buildLabelsData( Fingerprint $terms ) {
		$labels = $terms->getLabels()->toTextArray();

		$labelsArray = array();

		foreach ( $labels as $languageCode => $label ) {
			$labelsArray[$languageCode] = $label;
		}

		return $labelsArray;
	}

}
