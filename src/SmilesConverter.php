<?php

namespace Avogadrio;

use Condense\Database;
use \GuzzleHttp\Client;

/**
 * Provides a conversion service between compound names and SMILES strings.
 *
 * @author Saul Johnson <saul.a.johnson@gmail.com>
 * @since 06/08/2017
 * @package Avogadrio
 */
class SmilesConverter
{
    /**
     * The compound name/SMILES cache database.
     *
     * @var Database
     */
    private $db;

    /**
     * Initializes a new instance of a conversion service between compound names and SMILES strings.
     *
     * @param Database $db  the database in which to cache lookup results
     */
    public function __construct($db = null) {
        $this->db = $db;
    }

    /**
     * Gets whether or not the API result cache is enabled.
     *
     * @return bool true if the cache is enabled, otherwise false
     */
    public function isCacheEnabled() {
        return $this->db !== null;
    }

    /**
     * Converts a compound name to SMILES notation.
     *
     * @param string $name  the name of the compound
     * @return null|string  the SMILES notation for the named compound or null if not found
     */
    public function nameToSmiles($name)
    {
        // Sanitize name for URLs.
        $encoded = rawurlencode($name);

        // Check if we've got the name cached already.
        if ($this->isCacheEnabled()) {
            $cachedSmiles = $this->db->get('smiles', 'name', $encoded);
            if ($cachedSmiles !== null) {
                return $cachedSmiles;
            }
        }

        // Convert chemical name to SMILES if we can using API.
        $client = new Client(['verify' => false, 'exceptions' => false]);
        $response = $client->request('GET', "https://cactus.nci.nih.gov/chemical/structure/$encoded/smiles");

        // If request was successful.
        if ($response->getStatusCode() === 200) {
            $smiles = $response->getBody()->getContents();
            if ($this->isCacheEnabled()) {
                $this->db->insert(['name' => $encoded, 'smiles' => $smiles]); // Cache name for future.
            }
            return $smiles;
        }

        // Request failed.
        return null;
    }
}
