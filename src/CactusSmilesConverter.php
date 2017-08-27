<?php

namespace Avogadrio;

use Condense\Database;
use \GuzzleHttp\Client;

/**
 * Provides a conversion service between compound names and SMILES strings that uses a dedicated lookup service.
 *
 * @author Saul Johnson <saul.a.johnson@gmail.com>
 * @since 06/08/2017
 * @package Avogadrio
 */
class CactusSmilesConverter extends SmilesConverter
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
        $encoded = rawurlencode($name);

        // Check if we've got the name cached already.
        $cached = $this->getIfCachedAndValid($encoded);
        if ($cached !== null) {
            return $cached;
        }

        // Convert chemical name to SMILES if we can using API.
        $client = new Client(['verify' => false, 'exceptions' => false]);
        $response = $client->request('GET', "https://cactus.nci.nih.gov/chemical/structure/$encoded/smiles");

        // If request was successful.
        if ($response->getStatusCode() === 200) {
            $smiles = $response->getBody()->getContents();
            if (self::isValidSmiles($smiles)) {
                $this->cache($encoded, $smiles);
                return $smiles;
            }
        }

        return $this->fallback($name); // Fall back.
    }
}
