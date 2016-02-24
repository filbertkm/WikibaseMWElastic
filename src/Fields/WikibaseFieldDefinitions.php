<?php

namespace Wikibase\Elastic\Fields;

use InvalidArgumentException;

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
		$this->validateFieldNames( $fieldNames );

		$this->fieldNames = $fieldNames;
		$this->languageCodes = $languageCodes;
	}

	/**
	 * @return Field[] Array key is field name.
	 */
	public function getFields() {
		$fields = array();

		foreach ( $this->fieldNames as $fieldName ) {
			foreach ( $this->languageCodes as $languageCode ) {
				$field = $this->newFieldFromType( $fieldName, $languageCode );
				$name = $field->getFieldName();

				$fields[$name] = $field;
			}
		}

		return $fields;
	}

	private function validateFieldNames( array $fieldNames ) {
		foreach( $fieldNames as $fieldName ) {
			if ( ! in_array( $fieldName, array( 'labels', 'descriptions' ) ) ) {
				throw new InvalidArgumentException( 'Unknown field name: ' . $fieldName );
			}
		}
	}

	private function newFieldFromType( $type, $languageCode ) {
		switch( $type ) {
			case 'labels':
				return new LabelField( $languageCode );
			case 'descriptions':
				return new DescriptionField( $languageCode );
			default:
				throw new InvalidArgumentException( 'Unknown field type: ' . $type );
		}
	}

}
