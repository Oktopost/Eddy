<?php
namespace Eddy\Base\Crawler;


interface ICrawler
{
	public function load(string $eventsDir, string $handlersDir): bool;
}