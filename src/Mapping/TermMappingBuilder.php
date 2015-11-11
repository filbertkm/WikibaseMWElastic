<?php

namespace Wikibase\Elastic\Mapping;

class TermMappingBuilder {

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

	public function getProperties() {
		return array(
			'labels' => array(
				'type' => 'nested',
				'properties' => $this->getLabelFields()
			),
		);
	}

	private function getLabelFields() {
		$fields = array();

		foreach ( $this->languageCodes as $languageCode ) {
			$fields[$languageCode] = array(
				'type' => 'string'
			);
		}

		return $fields;
	}

}
