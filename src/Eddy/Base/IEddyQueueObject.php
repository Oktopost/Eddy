<?php
namespace Eddy\Base;


use Eddy\Base\Config\INaming;


/**
 * @property string $Id
 * @property string	$Name
 * @property string	$State
 * @property float	$Delay
 * @property int	$MaxBulkSize
 * @property float	$DelayBuffer
 * @property int	$PackageSize
 */
interface IEddyQueueObject
{
	public function isActive(): bool;
	public function getQueueNaming(INaming $naming): string;
}