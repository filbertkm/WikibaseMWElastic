<?php

namespace Wikibase\Elastic\Fields;

class WikibaseFieldDefinitions {

	/**
	 * @var string[]
	 */
	private $fieldNames;

	/**
	 * @var string[]
	 */
	private $languageCodes;

	/**
	 * @param string[] $languageCodes
	 */
	public function __construct( array $fieldNames, array $languageCodes ) {
		$this->fieldNames = $fieldNames;
		$this->languageCodes = $languageCodes;
	}

	/**
	 * @return Field[] Array key is field name.
	 */
	public function getFields() {
		$fields = $this->getAvailableFields();

		foreach ( $fields as $fieldName => $field ) {
			if ( !in_array( $fieldName, $this->fieldNames ) ) {
				unset( $fields[$fieldName] );
			}
		}

		return $fields;
	}

	/**
	 * @return Field[] Array key is field name.
	 */
	private function getAvailableFields() {
		return array(
			'labels' => new LabelField( $this->languageCodes ),
			'descriptions' => new DescriptionField( $this->languageCodes )
		);
	}

}
