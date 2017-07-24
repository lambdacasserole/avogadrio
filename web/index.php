<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Intervention\Image\ImageManagerStatic as Image;

// Configure GD as image driver.
Image::configure(array('driver' => 'gd'));

// Load config.
$config = Spyc::YAMLLoad(__DIR__.'/../config/config.yaml');

$app = new Silex\Application();

// Uncomment the line below while debugging your app.
$app['debug'] = true;

// Twig initialization.
$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');
$twig = new Twig_Environment($loader, array(
    'cache' => __DIR__.'/../cache',
));

/*
 * Rendering functions.
 */

function renderMolecule($color, $smiles) {
    // Proxy into Sourire for molecule render.
    $molecule = Image::make('http://localhost:8080/molecule/' . urlencode($smiles));

    // Colorize molecule.
    list($r, $g, $b) = sscanf($color, "%02x%02x%02x");
    $unit = 100 / 255;
    $molecule->colorize($unit * $r, $unit * $g, $unit * $b);

    return $molecule; // Return molecule image.
}

function renderScaledMolecule($canvasWidth, $canvasHeight, $color, $smiles) {
    $molecule = renderMolecule($color, $smiles);
    $proportion = 0.6;
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

function nameToSmiles($config, $name) {
    // Convert chemical name to SMILES if we can.
    $client = new GuzzleHttp\Client(['verify' => false, 'exceptions'=>false]);
    $res = $client->request('GET', str_replace('$name', $name, $config['chem_name_lookup_service']));

    return $res->getStatusCode() == 200 ? $res->getBody() : null;
}

/*
 * Route actions.
 */

$app->get('/', function () use ($twig, $config) {
    return $twig->render('index.html.twig', $config);
});

$app->get('/api/smiles/{width}/{height}/{bgcolor}/{fgcolor}/{smiles}', function ($width, $height, $bgcolor, $fgcolor, $smiles) use ($twig, $config) {
    
    // Set up background.
    $img = Image::canvas($width, $height, "#$bgcolor");
    
    // Render molecule.
    $molecule = renderScaledMolecule($width, $height, $fgcolor, $smiles);
    
    // Center on background.
    $img->insert($molecule, 'center');
    
    // Send image out
    echo $img->response();
});

$app->get('/api/name/{width}/{height}/{bgcolor}/{fgcolor}/{name}', function ($width, $height, $bgcolor, $fgcolor, $name) use ($app, $twig, $config) {
    
    // Convert chemical name to SMILES if we can.
    $smiles = nameToSmiles($config, $name);

    // Forward  to SMILES route.
    if ($smiles !== null) {
        $smilesRequest = Request::create("/api/smiles/$width/$height/$bgcolor/$fgcolor/$smiles", 'GET');
        return $app->handle($smilesRequest, HttpKernelInterface::SUB_REQUEST);
    }

    // Invalid chemical name.
    return $app->abort(404, "Chemical name could not be converted to SMILES.");
});

$app->get('/api/smiles/{width}/{height}/{color}/{smiles}', function ($width, $height, $color, $smiles) use ($app, $twig, $config) {
    echo renderScaledMolecule($width, $height, $color, $smiles)->response();
});

$app->get('/api/name/{width}/{height}/{color}/{name}', function ($width, $height, $color, $name) use ($app, $twig, $config) {
    
    // Convert chemical name to SMILES if we can.
    $smiles = nameToSmiles($config, $name);

    // Forward  to SMILES route.
    if ($smiles !== null) {
        $smilesRequest = Request::create("/api/smiles/$width/$height/$color/$smiles", 'GET');
        return $app->handle($smilesRequest, HttpKernelInterface::SUB_REQUEST);
    }

    // Invalid chemical name.
    return $app->abort(404, "Chemical name could not be converted to SMILES.");
});

$app->run();
