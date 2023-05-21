<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Updater\UpdatePatcher;

return new class extends UpdatePatcher
{
    public function run(): void
    {
        if (! $this->getPhonesTableNumberColumnIndexName()) {
            Schema::table('phones', function (Blueprint $table) {
                $table->index('number');
            });
        }
    }

    public function shouldRun(): bool
    {
        return ! $this->getPhonesTableNumberColumnIndexName();
    }

    protected function getPhonesTableNumberColumnIndexName()
    {
        foreach ($this->getColumnIndexes('phones', 'number') as $key) {
            if (str_ends_with($key->Key_name, 'number_index') && $key->Index_type == 'BTREE') {
                return $key->Key_name;
            }
        }
    }
};
