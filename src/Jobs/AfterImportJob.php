<?php

namespace robman2100\Excel\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use robman2100\Excel\Reader;
use robman2100\Excel\HasEventBus;
use robman2100\Excel\Concerns\WithEvents;
use robman2100\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;

class AfterImportJob implements ShouldQueue
{
    use Queueable, HasEventBus;

    /**
     * @var WithEvents
     */
    private $import;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param object $import
     * @param Reader $reader
     */
    public function __construct($import, Reader $reader)
    {
        $this->import = $import;
        $this->reader = $reader;
    }

    public function handle()
    {
        if ($this->import instanceof WithEvents) {
            $this->reader->registerListeners($this->import->registerEvents());
        }

        $this->reader->afterImport($this->import);
    }

    /**
     * @param Throwable $e
     */
    public function failed(Throwable $e)
    {
        if ($this->import instanceof WithEvents) {
            $this->registerListeners($this->import->registerEvents());
            $this->raise(new ImportFailed($e));

            if (method_exists($this->import, 'failed')) {
                $this->import->failed($e);
            }
        }
    }
}
