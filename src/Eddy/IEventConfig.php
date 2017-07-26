<?php
namespace Eddy;


interface IEventConfig
{
	public function name(): string;
	public function delay(): ?float;
	public function initialState(): string;
	public function eventClassName(): string;
	public function proxyClassName(): ?string;
	public function handlersInterface(): string;
	public function prepare(array $data): ?array;
}