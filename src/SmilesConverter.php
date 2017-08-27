<?php

namespace Avogadrio;

use Condense\Database;

/**
 * Provides a conversion service between compound names and SMILES strings.
 *
 * @author Saul Johnson <saul.a.johnson@gmail.com>
 * @since 27/08/2017
 * @package Avogadrio
 */
abstract class SmilesConverter
{
    /**
     * The compound name/SMILES cache database.
     *
     * @var Database
     */
    private $db;

    /**
     * The conversion service to fall back to in case of error.
     *
     * @var SmilesConverter
     */
    private $fallback;

    /**
     * Initializes a new instance of a conversion service between compound names and SMILES strings.
     *
     * @param Database $db              the database in which to cache lookup results
     * @param SmilesConverter $fallback the conversion service to fall back to in case of error
     */
    protected function __construct($db, $fallback) {
        $this->db = $db;
        $this->fallback = $fallback;
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
     * Attempts to fix any errors in a SMILES string and returns the result.
     *
     * @param string $smiles    the SMILES string to fix
     * @return string           the fixed SMILES string
     */
    protected static function fixSmiles($smiles) {
        $output = str_replace('|', '', $smiles); // Zap vertical bars.
        return $output;
    }

    /**
     * Returns true if a given SMILES string is valid, otherwise returns false.
     *
     * @param string $smiles    the SMILES string to check
     * @return bool             true if the string was valid, otherwise false
     */
    protected static function isValidSmiles($smiles) {
        return preg_match('/^[a-zA-Z0-9@%=\\\\\/\[\]\.\+\-\_\*\(\)]+$/', $smiles) ? true : false;
    }

    /**
     * @param $name
     * @return string
     */
    protected function getIfCachedAndValid($name) {
        if ($this->isCacheEnabled()) {
            $cachedSmiles = $this->db->get('smiles', 'name', $name);
            if ($cachedSmiles !== null && self::isValidSmiles($cachedSmiles)) {
                return $cachedSmiles;
            }
        }
        return null;
    }

    protected function cache($name, $smiles) {
        if ($this->isCacheEnabled()) {
            $this->db->insert(['name' => $name, 'smiles' => $smiles]); // Cache name for future.
        }
    }

    /**
     * Falls back to another SMILES conversion service.
     *
     * @param string $name  the compound name to convert
     * @return null|string  the converted compound name or null if not found
     */
    protected function fallback($name) {
        if ($this->fallback === null) {
            return null;
        }
        return $this->fallback->nameToSmiles($name);
    }

    /**
     * Converts a compound name to SMILES notation.
     *
     * @param string $name  the name of the compound
     * @return null|string  the SMILES notation for the named compound or null if not found
     */
    public abstract function nameToSmiles($name);
}
