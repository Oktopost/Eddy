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
}