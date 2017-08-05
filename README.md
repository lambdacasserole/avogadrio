# Avogadrio
Worship your favorite molecule by setting it as your wallpaper.

Avogadrio is a web app that will render your favourite molecule as a desktop wallpaper from either a compund name or
[SMILES structure](https://en.wikipedia.org/wiki/Simplified_molecular-input_line-entry_system). Molecule rendering
is designed to be powered by [Sourire](https://github.com/tmoerman/sourire) which itself wraps the
[Indigo cheminformatics toolkit](https://github.com/ggasoftware/indigo).

![Screenshot](screenshot_small.png)

## Prerequisites
You'll need to have a web server installed and configured with PHP for this to work. I really recommend [XAMPP](https://www.apachefriends.org/), especially for Windows users. Once you've done that you can proceed.

You'll also need [Node.js](https://nodejs.org/en/) and [npm](https://www.npmjs.com/) installed and working.

## Configuration
A couple of files need changing to get the site working for you.

1. Copy `config/config.yaml.dist` and rename it to `config.yaml`. Fill in the fields according to their descriptions and save.
2. For `chem_name_lookup_service` I recommend setting it to `https://cactus.nci.nih.gov/chemical/structure/$name/smiles`.
3. For `sourire_service` you'll want to point it to your local [Sourire](https://github.com/tmoerman/sourire) server. For example `http://localhost:8080/molecule/$smiles`.
4. Create a file at `templates/_analytics.txt` for your analytics code. Leave it blank if you like, but you must create it.

## Building
Clone the project down and open the folder in your favourite editor. It's a JetBrains PhpStorm project but you can use whichever paid/free software takes your fancy.

Before anything else, note that this project uses the [Composer](https://getcomposer.org/) package manager. Install composer (see their website) and run:

```
composer install
```

Or alternatively, if you're using the PHAR (make sure the `php.exe` executable is in your PATH):

```
php composer.phar install
```

Then, install the npm packages necessary to build and run the website. Run the following in your terminal in the project root directory:

```
npm install
```

This will install [Bower](https://bower.io/) which will allow you to install the assets the website requires (Bootstrap, jQuery etc.) using the command:

```
bower install
```

Gulp will also have been installed. This will compile the [Less](http://lesscss.org/) and [CoffeeScript](http://coffeescript.org/) into CSS and JavaScript ready for production. Do this using the command:

```
gulp
```

This command will need running again every time you make a change to a Less or CoffeeScript file. If you're working on them, run `gulp watch` in a terminal to watch for file changes and compile accordingly.

## Acknowledgements

[Jay Holtslander](https://codepen.io/j_holtslander/) put together the hamburger menu/sidebar combo in his [Pen](https://codepen.io/j_holtslander/pen/XmpMEp) on CodePen. I believe it's derived from earlier work by [maridlcrmn](https://bootsnipp.com/maridlcrmn).
