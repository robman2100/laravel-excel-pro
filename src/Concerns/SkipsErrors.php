<?php

namespace robman2100\Excel\Concerns;

use Throwable;
use Illuminate\Support\Collection;
use robman2100\Excel\Validators\Failure;

trait SkipsErrors
{
    /**
     * @var Failure[]
     */
    protected $errors = [];

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = $e;
    }

    /**
     * @return Throwable[]|Collection
     */
    public function errors(): Collection
    {
        return new Collection($this->errors);
    }
}
