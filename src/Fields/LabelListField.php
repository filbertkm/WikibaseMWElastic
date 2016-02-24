<?php

namespace Wikibase\Elastic\Fields;

use Wikibase\DataModel\Entity\EntityDocument;

class LabelListField extends TermListField implements Field {

	/**
	 * @param EntityDocument $entity
	 *
	 * @return array
	 */
	public function getFieldData( EntityDocument $entity ) {
		$terms = $entity->getFingerprint();

		return $this->buildTermsData( $terms->getLabels() );
	}

	/**
	 * @return string
	 */
	protected function getPrefix() {
		return 'label';
	}

}
