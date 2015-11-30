<?php

namespace Wikibase\Elastic\Fields;

use Wikibase\DataModel\Entity\EntityDocument;

class DescriptionField extends TermListField implements Field {

	/**
	 * @param EntityDocument $entity
	 *
	 * @return array
	 */
	public function buildData( EntityDocument $entity ) {
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
