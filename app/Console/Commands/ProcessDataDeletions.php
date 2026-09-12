<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DataRequest;
use App\Models\User;
use App\Services\DataDeletionService;
use Illuminate\Console\Command;

class ProcessDataDeletions extends Command
{
    protected $signature = 'data:process-deletions {--request= : Process one data request ID}';

    protected $description = 'Process approved data deletion requests.';

    public function handle(DataDeletionService $service): int
    {
        $query = DataRequest::query()
            ->where('type', DataRequest::TYPE_DELETION)
            ->where('status', DataRequest::STATUS_APPROVED)
            ->orderBy('id');

        if ($this->option('request') !== null) {
            $query->whereKey((int) $this->option('request'));
        }

        $actor = User::query()->where('is_admin', true)->orderBy('id')->first();
        if (! $actor instanceof User) {
            $this->error('No administrator is available to execute data deletion requests.');

            return self::FAILURE;
        }

        $processed = 0;
        foreach ($query->get() as $request) {
            try {
                $service->execute($request, $actor);
                $processed++;
            } catch (\Throwable $exception) {
                $this->error(sprintf('Request %d failed: %s', $request->id, $exception->getMessage()));
                if ($this->option('request') !== null) {
                    return self::FAILURE;
                }
            }
        }

        $this->info(sprintf('Processed %d deletion request(s).', $processed));

        return self::SUCCESS;
    }
}
