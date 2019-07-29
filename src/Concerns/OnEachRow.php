<?php

namespace robman2100\Excel\Concerns;

use robman2100\Excel\Row;

interface OnEachRow
{
    /**
     * @param Row $row
     */
    public function onRow(Row $row);
}
