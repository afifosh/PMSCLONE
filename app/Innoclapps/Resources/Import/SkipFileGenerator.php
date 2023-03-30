<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps\Resources\Import;

use App\Innoclapps\Models\Import;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;

class SkipFileGenerator implements FromArray
{
    /**
     * Skip reason heading name
     */
    const SKIP_REASON_HEADING = '--Skip Reason--';

    /**
     * Initialize new SkipFileGenerator instance
     *
     * @param \App\Innoclapps\Models\Import $import
     * @param \Illuminate\Support\Collection $failures
     * @param \Illuminate\Support\Collection $mappings
     */
    public function __construct(protected Import $import, protected $failures, protected $mappings)
    {
    }

    /**
     * Creates the import skip file
     *
     * @return array
     */
    public function array() : array
    {
        return [
            $this->headings(),
            ...$this->rows(),
        ];
    }

    /**
     * Store the skip file in storage
     *
     * @return string
     */
    public function store()
    {
        $path = $this->import->storagePath($this->filename());

        Excel::store($this, $path, $this->import::disk());

        return $path;
    }

    /**
     * Get the skip file filename
     *
     * @return string
     */
    public function filename() : string
    {
        $filename = basename($this->import->file_path);

        if (! str_starts_with($filename, 'skip-file-')) {
            $filename = 'skip-file-' . $filename;
        }

        return $filename;
    }

    /**
     * Group all of the validation errors grouped per row
     *
     * @return array
     */
    protected function errors() : array
    {
        $grouped = [];

        foreach ($this->failures as $failure) {
            $grouped[$failure->row()] = array_merge(
                $grouped[$failure->row()] ?? [],
                $failure->errors()
            );
        }

        return $grouped;
    }

    /**
     * Get the skip file headings
     *
     * @return array
     */
    public function headings() : array
    {
        return $this->mappings->pluck('original')
                ->forget(static::SKIP_REASON_HEADING)
                ->prepend(static::SKIP_REASON_HEADING)
                ->all();
    }

    /**
     * Get the skip file rows
     *
     * @return array
     */
    public function rows() : array
    {
        $errors = $this->errors();

        return $this->failures->unique(fn (Failure $failure) => $failure->row())
            ->map(function (Failure $failure) use ($errors) {
                return [
                    implode(PHP_EOL, $errors[$failure->row()]),
                    ...array_values($failure->values()['_original']),
                ];
            })->all();
    }
}
