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

	public function getFields() {
		return array(
			'labels' => array(
				'type' => 'nested',
				'properties' => $this->getLabelProperties()
			),
		);
	}

	private function getLabelProperties() {
		$fields = array();

		foreach ( $this->languageCodes as $languageCode ) {
			$fields[$languageCode] = array(
				'type' => 'string'
			);
		}

		return $fields;
	}

}
