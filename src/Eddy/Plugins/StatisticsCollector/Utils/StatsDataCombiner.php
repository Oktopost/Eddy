<?php
namespace Eddy\Plugins\StatisticsCollector\Utils;


class StatsDataCombiner
{
	/**
	 * @param mixed $value
	 * @param mixed $key
	 */
	private function canCombine($value, array $newData, $key, array $ignoreFields): bool
	{
		return is_numeric($value) && isset($newData[$key]) && !in_array($key, $ignoreFields);
	}
	
	private function getKey(array $entry): string
	{
		return StatsKeyBuilder::getForCombine($entry['Type'], $entry['Name']);
	}
	
	private function combineForObject(array $entries, string $date, int $granularity): array
	{
		$firstEntry = array_shift($entries);
		
		foreach ($entries as $entry)
		{
			$firstEntry = $this->combine($firstEntry, $entry);
		}
		
		$firstEntry['DataDate'] = $date;
		$firstEntry['Granularity'] = $granularity;
		
		return $firstEntry;
	}
	
	
	public function combine(array $data, array $dataToAdd, array $ignoreFields = []): array
	{
		foreach ($data as $key => $value)
		{
			if ($this->canCombine($value, $dataToAdd, $key, $ignoreFields))
			{
				$dataToAdd[$key] += $value;
			}
		}
		
		return $dataToAdd;
	}
	
	public function combineAll(array $data, string $date, int $granularity): array
	{
		$sortedEntries = [];
		
		foreach ($data as $entry)
		{
			$sortedEntries[$this->getKey($entry)][] = $entry;
		}
		
		$data = [];
		
		foreach ($sortedEntries as $sortedEntry)
		{
			$data[] = $this->combineForObject($sortedEntry, $date, $granularity);
		}
		
		return $data;
	}
}