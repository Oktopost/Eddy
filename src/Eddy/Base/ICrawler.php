<?php

namespace Eddy\Base;


interface ICrawler
{
	public function getEvents(): array;
	public function getHandlers(): array;
}