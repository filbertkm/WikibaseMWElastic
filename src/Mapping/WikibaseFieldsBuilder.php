<?php

namespace Wikibase\Elastic\Mapping;

class WikibaseFieldsBuilder {

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
				'properties' => $this->getTermFieldProperties()
			),
			'descriptions' => array(
				'type' => 'nested',
				'properties' => $this->getTermFieldProperties()
			),
			'entity_type' => array(
				'type' => 'string'
			),
			'sitelink_count' => array(
				'type' => 'long'
			),
		);
	}

	private function getTermFieldProperties() {
		$fields = array();

		foreach ( $this->languageCodes as $languageCode ) {
			$fields[$languageCode] = array(
				'type' => 'string'
			);
		}

		return $fields;
	}

}
