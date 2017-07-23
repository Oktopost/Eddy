<?php
namespace Example\Events\PostCreated;


use Eddy\Base\Event\IEventInterface;


interface PostCreated extends IEventInterface
{
	public function onPostCreated(string $postId): void;
	public function onPostCreatedBulk(array $postIds): void;
}