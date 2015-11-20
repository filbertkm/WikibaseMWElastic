<?php

namespace Wikibase\Elastic\Hooks;

use CirrusSearch\Maintenance\MappingConfigBuilder;
use Wikibase\Elastic\Mapping\WikibaseFieldsBuilder;
use Wikibase\Lib\WikibaseContentLanguages;

class MappingConfigHookHandler {

	/**
	 * @var WikibaseFieldsBuilder
	 */
	private $fieldsBuilder;

	/**
	 * @param array &$config
	 * @param MappingConfigBuilder $mappingConfigBuilder
	 *
	 * @return bool
	 */
	public static function onCirrusSearchMappingConfig(
		array &$config,
		MappingConfigBuilder $mappingConfigBuilder
	) {
		$handler = self::newFromGlobalState();
		$handler->addExtraFields( $config );

		return true;
	}

	private static function newFromGlobalState() {
		$contentLanguages = new WikibaseContentLanguages();

		return new self(
			new WikibaseFieldsBuilder( $contentLanguages->getLanguages() )
		);
	}

	/**
	 * @param WikibaseFieldsBuilder $fieldsBuilder
	 */
	public function __construct( WikibaseFieldsBuilder $fieldsBuilder ) {
		$this->fieldsBuilder = $fieldsBuilder;
	}

	/**
	 * @param array &$config
	 */
	public function addExtraFields( array &$config ) {
		$fields = $this->fieldsBuilder->getFields();

		foreach ( $fields as $property => $propertyFields ) {
			$config['page']['properties'][$property] = $propertyFields;
		}
	}

}
