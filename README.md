# Rack'em Sass

Rack'em middleware to serve SASS dynamically.

## Usage

```php
<?php
# config.php

// Rack up the middleware and you are good to go!
\Rackem::use_middleware(new \Rackem\Sass());


// or map to a specific path
\Rackem::map("/css", function($env) {
	$sass = new \Rackem\Sass\Environment();
	$sass->append_path("src/sass");

	return $sass->call($env);
});

```

## Options

You can pass an options `array()` with the middleware, or to a `Rackem\Sass\Enviroment` instance.

| Option | Info | Default |
|--------|------|---------|
| __accepts__ | File extensions used to locate sass source files (order matters). | `array("sass", "scss", "scss.css", "sass.css", "css")` |
| __paths__ | Paths used to locate sass source files (order matters). | `array(".")` |
| __persist__ | If true, will serve compiled css file directly. If source sass file is newer than compiled file, the sass will be re-compiled before being served. | `false` |
| __public__ | Path used to write compiled css if `persist` option is true. | `getcwd()` |
| __parser__ | Array of options passed into `Sass\Parser`. | `array("cache" => false)` |
