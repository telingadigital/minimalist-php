<?php
namespace App\Controllers;

use Twig_Environment;

class HomeController
{

	private $twig;

	public function __construct(Twig_Environment $twig)
	{
		$this->twig = $twig;
	}

	public function index()
	{
		echo $this->twig->render('pages/index.twig');
	}
}