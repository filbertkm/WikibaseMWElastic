<?php

namespace Wikibase\Elastic\Fields;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Term\TermList;

abstract class TermListField implements Field {

	/**
	 * @var string[]
	 */
	private $languageCodes;

	/**
	 * @param string[] $languageCodes
	 */
	public function __construct( array $languageCodes ) {
		$this->languageCodes = $languageCodes;
	}

	/**
	 * @return array
	 */
	public function getMapping() {
		return array(
			'type' => 'nested',
			'properties' => $this->getTermFieldProperties()
		);
	}

	/**
	 * @param EntityDocument $entity
	 *
	 * @return array
	 */
	abstract public function getFieldData( EntityDocument $entity );

	/**
	 * @return string
	 */
	abstract protected function getPrefix();

	/**
	 * @return array
	 */
	protected function getTermFieldProperties() {
		$prefix = $this->getPrefix();
		$fields = array();

		foreach ( $this->languageCodes as $languageCode ) {
			$fields[$prefix . '_' . $languageCode] = array(
				'type' => 'string'
			);
		}

		return $fields;
	}

	/**
	 * @param TermList $terms
	 *
	 * @return array
	 */
	protected function buildTermsData( TermList $terms ) {
		$prefix = $this->getPrefix();
		$termsArray = array();

		foreach ( $terms->toTextArray() as $languageCode => $term ) {
			$termsArray[$prefix . '_' . $languageCode] = $term;
		}

		return $termsArray;
	}

}
