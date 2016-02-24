<?php

namespace Wikibase\Elastic\Fields;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Statement\StatementListHolder;

class StatementCountField implements Field {

	/**
	 * @return array
	 */
	public function getMapping() {
		return array(
			'type' => 'long'
		);
	}

	/**
	 * @param EntityDocument $entity
	 *
	 * @return mixed
	 */
	public function getFieldData( EntityDocument $entity ) {
		if ( $entity instanceof StatementListHolder ) {
			return $entity->getStatements()->count();
		}

		return 0;
	}

}