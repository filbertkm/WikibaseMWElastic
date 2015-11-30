<?php

namespace Wikibase\Elastic\Fields;

class WikibaseFieldsDefinition {

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
	 * @return Field[] Array key is field name.
	 */
	public function getFields() {
		return array(
			'labels' => new LabelField( $this->languageCodes ),
			'descriptions' => new DescriptionField( $this->languageCodes ),
			'sitelink_count' => new SiteLinkCountField(),
			'statement_count' => new StatementCountField()
		);
	}

}
