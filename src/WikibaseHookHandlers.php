<?php

namespace Wikibase\Search\Elastic\Hooks;

use Wikibase\EntityContent;

/**
 * Extension hooks
 */
class WikibaseHookHandlers {

	/**
	 * @param EntityContent $content
	 * @param string &$text
	 */
	public static function onWikibaseTextForSearchIndex( EntityContent $content, &$text ) {
		$text = '';

		return false;
	}

}
