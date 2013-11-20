<?php
namespace Rackem;

class Sass extends \Rackem\Middleware
{
	/*
	 * Create a Sass\Environment application and call it.
	 *
	 * Environment returns a 200 on success and stops the stack, otherwise
	 * the middleware is skipped and continues down the stack.
	 */
	public function call($env)
	{
		$sass = new Sass\Environment($this->options);

		list($status, $headers, $body) = $sass->call($env);
		if($status === 200) return array($status, $headers, $body);
		return $this->app->call($env);
	}
}
