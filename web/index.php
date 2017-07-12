<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Intervention\Image\ImageManagerStatic as Image;

Image::configure(array('driver' => 'gd'));

$config = Spyc::YAMLLoad(__DIR__.'/../config/config.yaml'); // Load config.

$app = new Silex\Application();

// Uncomment the line below while debugging your app.
$app['debug'] = true;

// Twig initialization.
$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');
$twig = new Twig_Environment($loader, array(
    'cache' => __DIR__.'/../cache',
));

/*
 * Route actions.
 */

$app->get('/', function () use ($twig, $config) {
    return $twig->render('index.html.twig', $config);
});

$app->get('/contact', function () use ($twig, $config) {
    return $twig->render('contact.html.twig', $config);
});

$app->post('/contact', function () use ($twig, $config) {
    // Collect form fields.
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Validate form.
    $errors = [];
    if (strlen($name) < 2) {
        $errors[] = 'The name you provide needs to 2 or more characters in length.';
    }
    if (strlen($message) < 30) {
        $errors[] = 'The message you submit needs to be 30 or more characters in length.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'The email address you provided is invalid.';
    }

    // Send message if no errors.
    if (sizeof($errors) == 0) {
        
        // Prepare headers.
        $headers =  'MIME-Version: 1.0' . "\r\n"; 
        $headers .= 'From: ' . $config['contact_form_from_name'] . ' <' . $config['contact_form_from'] . '>' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 

        mail($config['contact_form_target'], 'Website contact', "Name: $name\r\nWebsite: $website\r\nEmail: $email\r\nMessage: $message", $headers);
    }

    // Render contact page with success/error message.
    $vars = array_merge($config, array(
        'success' => sizeof($errors) == 0,
        'errors' => $errors,
        'post' => $_POST
    ));
    return $twig->render('contact.html.twig', $vars);
});

$app->get('/linkedin', function () use ($twig, $config) {
    header('Location: ' . $config['linkedin_url']);
    die();
});

$app->get('/twitter', function () use ($twig, $config) {
    header('Location: ' . $config['twitter_url']);
    die();
});

$app->get('/github', function () use ($twig, $config) {
    header('Location: ' . $config['github_url']);
    die();
});

$app->get('/project-1', function () use ($twig, $config) {
    return $twig->render('project-1.html.twig', $config);
});

$app->get('/project-2', function () use ($twig, $config) {
    return $twig->render('project-2.html.twig', $config);
});

$app->get('/project-3', function () use ($twig, $config) {
    return $twig->render('project-3.html.twig', $config);
});

$app->get('/blog', function () use ($twig, $config) {
    return $twig->render('blog.html.twig', $config);
});

$app->get('/api/smiles/{width}/{height}/{bgcolor}/{fgcolor}/{smiles}', function ($width, $height, $bgcolor, $fgcolor, $smiles) use ($twig, $config) {
    
    // Set up background.
    $img = Image::canvas($width, $height, "#$bgcolor");
    
    // Proxy into Sourire for molecule render.
    $molecule = Image::make('http://localhost:8080/molecule/' . urlencode($smiles));
    
    // Colorize molecule.
    list($r, $g, $b) = sscanf($fgcolor, "%02x%02x%02x");
    $n = 100 / 255;
    $molecule->colorize($n * $r, $n * $g, $n * $b);
   
    // Center molecule on background.
    $img->insert($molecule, 'center');
    
    // Send image out
    echo $img->response();
});

$app->get('/api/name/{width}/{height}/{bgcolor}/{fgcolor}/{name}', function ($width, $height, $bgcolor, $fgcolor, $name) use ($app, $twig, $config) {
    
    // Convert chemical name to SMILES if we can.
    $client = new GuzzleHttp\Client(['verify' => false, 'exceptions'=>false]);
    $res = $client->request('GET', str_replace('$name', $name, $config['chem_name_lookup_service']));

    if ($res->getStatusCode() == 200) {
        
        // Forward  to SMILES route.
        $smiles = $res->getBody();
        $smilesRequest = Request::create("/api/smiles/$width/$height/$bgcolor/$fgcolor}/$smiles", 'GET');
        return $app->handle($smilesRequest, HttpKernelInterface::SUB_REQUEST);
    } else {
        
        // Invalid chemical name.
        $app->abort(404, "Chemical name could not be converted to SMILES.");
    }
});

$app->run();
