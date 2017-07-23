<?php
namespace Eddy\Crawler\Base;


interface ICrawler
{
	public function load(string $eventsDir, string $handlersDir): bool;
}