<?php

namespace Avogadrio;

use Condense\Database;
use \GuzzleHttp\Client;
use pQuery;

/**
 * Provides a conversion service between compound names and SMILES strings that scrapes SMILES strings from Wikipedia.
 *
 * @author Saul Johnson <saul.a.johnson@gmail.com>
 * @since 27/08/2017
 * @package Avogadrio
 */
class WikipediaSmilesConverter extends SmilesConverter
{
    /**
     * Initializes a new instance of a conversion service between compound names and SMILES strings.
     *
     * @param Database $db              the database in which to cache lookup results
     * @param SmilesConverter $fallback the conversion service to fall back to in case of error
     */
    public function __construct($db = null, $fallback = null) {
        parent::__construct($db, $fallback);
    }

    /**
     * @inheritdoc
     */
    public function nameToSmiles($name)
    {
        // Sanitize name for URLs.
        $encoded = str_replace(' ', '_', $name);

        // Check if we've got the name cached already.
        $cached = $this->getIfCached($encoded);
        if ($cached !== null) {
            return $cached;
        }

        // Get Wikipedia page for compound.
        $client = new Client();
        $response = $client->request('GET', "https://en.wikipedia.org/wiki/$encoded");

        // If there is a page.
        if ($response->getStatusCode() === 200) {

            // Parse page into DOM.
            $page = $response->getBody()->getContents();
            $dom = pQuery::parseStr($page);

            // Get all hyperlinks.
            $links = $dom->select('a');

            // Look for the SMILES hyperlink.
            foreach ($links as $link) {
                if (strstr($link->text(), 'SMILES')) {
                    $smiles = trim($link->parent->getNextSibling()->text()); // Get actual SMILES string.
                    if (self::isValidSmiles($smiles)) {
                        $this->cache($encoded, $smiles);
                        return $smiles;
                    }
                }
            }
        }

        return $this->fallback($name); // Fall back.
    }
}
