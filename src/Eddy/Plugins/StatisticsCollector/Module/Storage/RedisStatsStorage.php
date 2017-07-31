<?php
namespace Eddy\Plugins\StatisticsCollector\Module\Storage;


use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;

use Predis\Client;


class RedisStatsStorage implements IStatisticsStorage
{
	private const STATS_PREFIX = 'stats';
	
	
	/** @var Client */
	private $client;
	
	
	private function buildKey(StatsEntry $entry): string
	{
		return self::STATS_PREFIX . ":{$entry->Type}:{$entry->Name}:{$entry->Time}";
	}
	

	public function __construct()
	{
		/** TODO: replace */
		$config = [
			'scheme'	=> 'tcp',
			'host'		=> '127.0.0.1',
			'port'		=> '6379',
		];
		
		$options = [
			'prefix'	=> 'statistics-temp'	
		];
		
		$this->client = new Client($config, $options);
	}


	public function save(StatsEntry $entry): void
	{
		$this->client->hmset($this->buildKey($entry), $entry->toArray());
	}

	public function pullData(int $endTime): array
	{
		
	}
}