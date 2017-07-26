<?php
namespace Eddy\Base;


use Eddy\Base\Config\INaming;


/**
 * @property string $Id
 * @property string	$Name
 * @property string	$State
 * @property float	$Delay
 * @property int	$MaxBulkSize
 */
interface IEddyQueueObject
{
	public function getQueueNaming(INaming $naming): string;
}