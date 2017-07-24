<?php
namespace Eddy;


interface IHandler
{
	public function delay(): float;
	public function initialState(): string;
	public function handlerClassName(): string;
	public function getInstance(): string;
}