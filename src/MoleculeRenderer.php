<?php

namespace Avogadrio;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Provides a molecule rendering service via a Sourire installation.
 *
 * @author Saul Johnson <saul.a.johnson@gmail.com>
 * @since 06/08/2017
 * @package Avogadrio
 */
class MoleculeRenderer
{
    private $sourireUrl;

    /**
     * Initializes a new instance of a molecule rendering service.
     *
     * @param string $sourireUrl    the URL of the running Sourire server to use
     */
    public function __construct($sourireUrl)
    {
        // Add missing forward slash if needed.
        $this->sourireUrl = $sourireUrl;
        if ($this->sourireUrl[strlen($this->sourireUrl) - 1] != '/') {
            $this->sourireUrl .= '/';
        }

        // Configure GD as image driver.
        Image::configure(array('driver' => 'gd'));
    }

    /**
     * Renders a molecule.
     *
     * @param string $smiles    the SMILES structure of the molecule to render
     * @param string $color     the color to render the molecule in (as a hex string without `#`)
     * @return \Intervention\Image\Image
     */
    public function renderMolecule($smiles, $color)
    {
        // Proxy into Sourire for molecule render.
        $img = Image::make($this->sourireUrl . urlencode($smiles));

        // Colorize molecule.
        list($r, $g, $b) = sscanf($color, "%02x%02x%02x");
        $unit = 100 / 255;
        $img->colorize($unit * $r, $unit * $g, $unit * $b);

        return $img; // Return molecule image.
    }

    /**
     * Renders a molecule, scaled to a canvas size using a maximum proportion.
     *
     * @param string $smiles    the SMILES structure of the molecule to render
     * @param string $color     the color to render the molecule in (as a hex string without `#`)
     * @param int $canvasWidth  the width of the canvas to scale the molecule to
     * @param int $canvasHeight the height of the canvas to scale the molecule to
     * @param float $proportion the maximum proportion of the canvas the molecule should occupy (in either direction)
     * @return \Intervention\Image\Image
     */
    public function renderScaledMolecule($smiles, $color, $canvasWidth, $canvasHeight, $proportion = 0.8)
    {
        // Render molecule at normal size.
        $img = $this->renderMolecule($smiles, $color);

        // Calculate proportions of canvas width.
        $px = $img->getWidth() / $canvasWidth;
        $py = $img->getHeight() / $canvasHeight;

        // Resize in both directions to fit.
        while ($px > $proportion || $py > $proportion) {
            if ($px > $proportion) {
                $factor = ($canvasWidth * $proportion) / $img->getWidth();
            } else {
                $factor = ($canvasHeight * $proportion) / $img->getHeight();
            }
            $img->resize($img->getWidth() * $factor, $img->getHeight() * $factor);
            $px = $img->getWidth() / $canvasWidth;
            $py = $img->getHeight() / $canvasHeight;
        }

        return $img;
    }

    /**
     * Renders a molecule, scaled to a canvas size using a maximum proportion.
     *
     * @param string $smiles the SMILES structure of the molecule to render
     * @param string $foreground the color to render the molecule in (as a hex string without `#`)
     * @param string $background the color to render the background in (as a hex string without `#`)
     * @param int $width the width of the image to render
     * @param int $height the height of the image to render
     * @return \Intervention\Image\Image
     */
    public function renderMoleculeWithBackground($smiles, $foreground, $background, $width, $height)
    {
        // Set up background.
        $img = Image::canvas($width, $height, "#$background");

        // Render scaled molecule.
        $molecule = $this->renderScaledMolecule($smiles, $foreground, $width, $height);

        // Center on background.
        $img->insert($molecule, 'center');

        return $img;
    }
}
