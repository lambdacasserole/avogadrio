<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Condense\Database;
use Avogadrio\SmilesConverter;
use Avogadrio\MoleculeRenderer;

// Load config.
$config = Spyc::YAMLLoad(__DIR__ . '/../config/config.yaml');

$app = new Silex\Application();

// Uncomment the line below while debugging your app.
$app['debug'] = true;

// Services.
$smilesConverter = new SmilesConverter(new Database('names', __DIR__ . '/../db'));
$moleculeRenderer = new MoleculeRenderer($config['sourire_service']);

// Twig initialization.
$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');
$twig = new Twig_Environment($loader, array(
    'cache' => false //__DIR__.'/../cache',
));

/*
 * Route actions.
 */

$app->get('/', function () use ($twig, $config) {
    return $twig->render('index.html.twig', $config);
});

$app->get('/api/smiles/{width}/{height}/{bgcolor}/{fgcolor}/{smiles}', function ($width, $height, $bgcolor, $fgcolor, $smiles) use ($twig, $config, $moleculeRenderer) {
    return new \Symfony\Component\HttpFoundation\Response(
        $moleculeRenderer->renderMoleculeWithBackground($fgcolor, $bgcolor, $smiles, $width, $height)->response('png'),
        200,
        ['Content-Type' => 'image/png']
    );
});

$app->get('/api/name/{width}/{height}/{bgcolor}/{fgcolor}/{name}', function ($width, $height, $bgcolor, $fgcolor, $name) use ($app, $smilesConverter) {
    
    // Convert chemical name to SMILES if we can.
    $smiles = $smilesConverter->nameToSmiles($name);

    // Forward to SMILES route.
    if ($smiles !== null) {
        $smilesRequest = Request::create("/api/smiles/$width/$height/$bgcolor/$fgcolor/$smiles", 'GET');
        return $app->handle($smilesRequest, HttpKernelInterface::SUB_REQUEST);
    }

    // Invalid chemical name.
    return $app->abort(404, "Chemical name could not be converted to SMILES.");
});

$app->get('/api/smiles/{width}/{height}/{color}/{smiles}', function ($width, $height, $color, $smiles) use ($app, $twig, $config, $moleculeRenderer) {
    return new \Symfony\Component\HttpFoundation\Response(
        $moleculeRenderer->renderScaledMolecule($width, $height, $color, $smiles)->response('png'),
        200,
        ['Content-Type' => 'image/png']
    );
});

$app->get('/api/name/exists/{name}', function ($name) use ($config, $smilesConverter) {
    return new JsonResponse($smilesConverter->nameToSmiles($name) === null ? false : true);
});

$app->get('/api/name/{width}/{height}/{color}/{name}', function ($width, $height, $color, $name) use ($app, $twig, $config, $smilesConverter) {
    
    // Convert chemical name to SMILES if we can.
    $smiles = $smilesConverter->nameToSmiles($name);

    // Forward  to SMILES route.
    if ($smiles !== null) {
        $smilesRequest = Request::create("/api/smiles/$width/$height/$color/$smiles", 'GET');
        return $app->handle($smilesRequest, HttpKernelInterface::SUB_REQUEST);
    }

    // Invalid chemical name.
    return $app->abort(404, "Chemical name could not be converted to SMILES.");
});

$app->run();
