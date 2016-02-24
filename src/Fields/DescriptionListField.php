<?php

namespace Wikibase\Elastic\Fields;

use Wikibase\DataModel\Entity\EntityDocument;

class DescriptionListField extends TermListField implements Field {

	/**
	 * @param EntityDocument $entity
	 *
	 * @return array
	 */
	public function getFieldData( EntityDocument $entity ) {
		$terms = $entity->getFingerprint();

		return $this->buildTermsData( $terms->getDescriptions() );
	}

	/**
	 * @return string
	 */
	protected function getPrefix() {
		return 'description';
	}

}
