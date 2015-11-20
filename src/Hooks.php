<?php

namespace Wikibase\Elastic;

use Wikibase\EntityContent;

/**
 * Extension hooks
 */
class Hooks {

	/**
	 * @param EntityContent $content
	 * @param string &$text
	 */
	public static function onWikibaseTextForSearchIndex( EntityContent $content, &$text ) {
		// $text = '';
	}

}
