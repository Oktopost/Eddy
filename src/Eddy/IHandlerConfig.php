<?php
namespace Eddy;


interface IHandlerConfig
{
	public function delay(): float;
	public function initialState(): string;
	public function handlerClassName(): string;
	public function getInstance(): string;
}