<?php

namespace Avogadrio;

use Intervention\Image\ImageManagerStatic as Image;

/**
 *
 *
 * @author Saul Johnson <saul.a.johnson@gmail.com>
 * @since 06/08/2017
 * @package Avogadrio
 */
class MoleculeRenderer
{
    private $sourireUrl;

    public function __construct($sourireUrl)
    {
        $this->sourireUrl = $sourireUrl;

        // Configure GD as image driver.
        Image::configure(array('driver' => 'gd'));
    }

    public function renderMolecule($color, $smiles)
    {
        // Proxy into Sourire for molecule render.
        $molecule = Image::make($this->sourireUrl . urlencode($smiles));

        // Colorize molecule.
        list($r, $g, $b) = sscanf($color, "%02x%02x%02x");
        $unit = 100 / 255;
        $molecule->colorize($unit * $r, $unit * $g, $unit * $b);

        return $molecule; // Return molecule image.
    }

    public function renderScaledMolecule($color, $smiles, $canvasWidth, $canvasHeight, $proportion = 0.6)
    {
        $molecule = $this->renderMolecule($color, $smiles);
        $px = $molecule->getWidth() / $canvasWidth;
        $py = $molecule->getHeight() / $canvasHeight;
        while ($px > $proportion || $py > $proportion) {
            if ($px > $proportion) {
                $factor = ($canvasWidth * $proportion) / $molecule->getWidth();
            } else {
                $factor = ($canvasHeight * $proportion) / $molecule->getHeight();
            }
            $molecule->resize($molecule->getWidth() * $factor, $molecule->getHeight() * $factor);
            $px = $molecule->getWidth() / $canvasWidth;
            $py = $molecule->getHeight() / $canvasHeight;
        }
        return $molecule;
    }

    public function renderMoleculeWithBackground($fgcolor, $bgcolor, $smiles, $width, $height) {
        // Set up background.
        $img = Image::canvas($width, $height, "#$bgcolor");

        // Render molecule.
        $molecule = $this->renderScaledMolecule($fgcolor, $smiles, $width, $height);

        // Center on background.
        $img->insert($molecule, 'center');

        return $img;
    }
}
