<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core;

class LogReader
{
    protected static ?string $glob = null;

    /**
     * Initialize new LogReader instance.
     */
    public function __construct(protected array $config = [])
    {
        $this->config['date'] = array_key_exists('date', $config) ? $config['date'] : null;
    }

    /**
     * Add custom glob reader
     */
    public static function glob(?string $glob): void
    {
        static::$glob = $glob;
    }

    /**
     * Get the available log file dates
     */
    public function getLogFileDates(): array
    {
        $dates = [];
        $files = glob(static::$glob ?: storage_path('logs/laravel-*.log'));
        $files = array_reverse($files);
        foreach ($files as $path) {
            $fileName = basename($path);
            preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);
            $date = $dtMatch[0];
            array_push($dates, $date);
        }

        return $dates;
    }

    /**
     * Get the log data
     */
    public function get(): array
    {
        $availableDates = $this->getLogFileDates();

        if (count($availableDates) == 0) {
            return [
                'success' => false,
                'message' => 'No logs available',
                'log_dates' => $availableDates,
            ];
        }

        $configDate = $this->config['date'];

        if ($configDate == null) {
            $configDate = $availableDates[0];
        }

        if (! in_array($configDate, $availableDates)) {
            return [
                'success' => false,
                'message' => 'No log file found with selected date '.$configDate,
                'log_dates' => $availableDates,
            ];
        }

        $pattern = "/^\[(?<date>.*)\]\s(?<env>\w+)\.(?<type>\w+):(?<message>.*)/m";

        $fileName = 'laravel-'.$configDate.'.log';
        $content = file_get_contents(storage_path('logs/'.$fileName));
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER, 0);

        $logs = [];

        foreach ($matches as $match) {
            $logs[] = [
                'timestamp' => $match['date'],
                'env' => $match['env'],
                'type' => $match['type'],
                'message' => trim($match['message']),
            ];
        }

        preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);

        $date = $dtMatch[0];

        return [
            'log_dates' => $availableDates,
            'date' => $date,
            'filename' => $fileName,
            'logs' => $logs,
        ];
    }
}
