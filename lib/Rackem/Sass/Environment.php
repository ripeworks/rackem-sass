<?php
namespace Rackem\Sass;

class Environment
{
	public $parser, $options;

	public function __construct($options = array())
	{
		$defaults = array(
			"accepts" => array(
				"scss",
				"sass",
				"scss.css",
				"sass.css",
				"css"
			),
			"paths" => array(
				"."
			),
			"parser" => array(
				"cache" => false
			),
            "persist" => false,
            "public" => getcwd()
		);
		$this->options = array_merge($defaults, $options);
	}

	public function accepts($file)
	{
		$file_parts = explode(".", $file);
		$extension = array_pop($file_parts);
        return $extension === "css";
	}

	public function append_path($path)
	{
		$this->options["paths"][] = $path;
	}

	public function call($env)
	{
		$req = substr($env['PATH_INFO'], 1);
		if(!$this->accepts($req)) return $this->fail(404);

		$source = $this->locate($req);
		if(!$source) return $this->fail(404);

        $public = rtrim($this->options["public"], "/");
        $file = "$public/$req";

        // persist file if persist = true
        if ($this->options["persist"] === true) {
            if ($this->needs_update($source, $file)) {
                $css = $this->parse($source);

                $handle = fopen($file, 'w');
                fwrite($handle, $css);
                fclose($handle);
            }
            $res = new \Rackem\File($public);
            return $res->call($env);
        }

        $css = $this->parse($source);
        return array(
            200,
            array(
                "Content-Type" => "text/css",
                "Content-Length" => strlen($css)
            ),
            array($css)
        );
	}

	public function fail($status = 404)
	{
		$body = "\n";
		return array(
			$status,
			array(
				"Content-Type" => "text/plain",
				"Content-Length" => strlen($body)
			),
			array($body)
		);
	}

	public function locate($file)
	{
		$name = basename($file, ".css");
		if(file_exists($name)) return $name;
		foreach($this->options["paths"] as $path)
		{
			$path = rtrim($path, "/");
			foreach($this->options["accepts"] as $possible_extension)
			{
				$test = "$path/$name.$possible_extension";
				if(file_exists($test)) return $test;
			}
		}
		return false;
	}

    public function needs_update($source, $target)
	{
		if(!file_exists($target)) return true;

		$s_date = filemtime($source);
		$t_date = filemtime($target);

		return ($s_date > $t_date);
	}

    public function parse($source)
    {
        $this->parser = new \SassParser($this->options["parser"]);
        return $this->parser->toCss($source);
    }
}
