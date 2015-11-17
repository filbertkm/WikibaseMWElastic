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
				'properties' => $this->getTermFieldProperties( 'label' )
			),
			'descriptions' => array(
				'type' => 'nested',
				'properties' => $this->getTermFieldProperties( 'description' )
			),
			'entity_type' => array(
				'type' => 'string'
			),
			'sitelink_count' => array(
				'type' => 'long'
			),

		);
	}

	private function getTermFieldProperties( $prefix ) {
		$fields = array();

		foreach ( $this->languageCodes as $languageCode ) {
			$fields[$prefix . '_' . $languageCode] = array(
				'type' => 'string'
			);
		}

		return $fields;
	}

}
