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
			)
		);
		$this->options = array_merge($defaults, $options);
	}

	public function accepts($file)
	{
		$file_parts = explode(".", $file);
		$extension = array_pop($file_parts);
		if(file_exists($file) && $extension == "css") return false;
		return true;
	}

	public function append_path($path)
	{
		$this->options["paths"][] = $path;
	}

	public function call($env)
	{
		$file = substr($env['PATH_INFO'], 1);
		if(!$this->accepts($file)) return $this->fail(404);

		$source = $this->locate($file);
		if(!$source) return $this->fail(404);

		$this->parser = new \SassParser($this->options["parser"]);
		$css = $this->parser->toCss($source);

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
}
