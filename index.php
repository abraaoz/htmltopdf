<?php

$composer = 'vendor/autoload.php';
if(is_readable($composer))
{
	require_once $composer;
}
else
{
	$path = __DIR__;
	die("Execute o comando 'composer install' na pasta {$path}.");
}

use mikehaertl\wkhtmlto\Pdf;

$url = isset($_GET['url'])
	? $_GET['url']
	: null;

// Valida a URL
if(!$url)
{
	die('O parâmetro GET "url" não foi fornecido.');
}

// Lê as configurações
$config = parse_ini_file('config.ini');

// Converte para PDF
$pdf = new Pdf([
	'binary' => "\"{$config['wkhtmltopdf']}\"",
	'tmpDir' => __DIR__ . '/temp',
	'commandOptions' => [
		'escapeArgs' => false,
		'procOptions' => [
			'bypass_shell' => true
		]
	]
]);
$pdf->addPage($url);

// Define o nome do arquivo
$dateTime = new DateTime();
$dateTime = $dateTime->format('Y-m-d-H-i-s');

if(!$pdf->send("{$dateTime}.pdf"))
{
	$error = $pdf->getError();

	// Oculta caminhos absolutos da mensagem de erro
	if(preg_match('/Failed without error message/', $error))
	{
		$error = 'O parâmetro GET "url" não parece ser uma URL válida.';
	}

	die($error);
}