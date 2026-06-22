<?php

class SearchHelper
{
    public static function searchArray(array $dataArray, string $keyword = '', array $columns = []): array
    {
        $keyword = trim(strtolower($keyword));

        if ($keyword === '') {
            return array_values($dataArray);
        }

        $filtered = array_filter($dataArray, function ($row) use ($keyword, $columns) {
            $targetColumns = !empty($columns) ? $columns : array_keys($row);

            foreach ($targetColumns as $column) {
                if (!isset($row[$column])) {
                    continue;
                }

                $value = $row[$column];

                if (is_array($value) || is_object($value)) {
                    continue;
                }

                $value = strtolower((string) $value);

                if (str_contains($value, $keyword)) {
                    return true;
                }
            }

            return false;
        });

        return array_values($filtered);
    }
}