<?php

namespace Wikibase\Search\Elastic\Search;

use CirrusSearch\Search\ResultSet;

/**
 * @license GPL 2.0+
 */
class SearchResultPrinter {

    public function print( ResultSet $results ) {
        $result = $results->next();

        while ( $result ) {
            echo $result->getTitle()->getPrefixedText() . "\n";
            $result = $results->next();
        }
    }

}
