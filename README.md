# Rack'em Sass

Rack'em middleware to serve SASS dynamically.

## Usage

```php
<?php
# config.php

// Rack up the middleware and you are good to go!
\Rackem::use_middleware(new \Rackem\Sass());


// Map to a specific path
\Rackem::map("/css", function($env) {
	$sass = new \Rackem\Sass\Environment();
	$sass->append_path("src/sass");

	return $sass->call($env);
});

```

## Options

| Option | Info | Default |
|--------|------|---------|
| __accepts__ | File extensions used to locate sass source files (order matters). | `array("sass", "scss", "scss.css", "sass.css", "css")` |
| __paths__ | Paths used to locate sass source files (order matters). | `array(".")` |
| __parser__ | Array of options passed into `Sass\Parser`. | `array("cache" => false)` |
