<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Commands;

use Dive\FeatureFlags\Contracts\Feature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class ListFeatureCommand extends Command
{
    protected $description = 'List all registered features.';

    protected $signature = 'feature:list
        {--compact : Only show state, scope & name fields}
        {--disabled : Indicates if only disabled features should be listed}
        {--enabled : Indicates if only enabled features should be listed}
        {--scope= : Only display features for a single scope}
    ';

    protected array $headers = ['state', 'name', 'scope', 'label', 'description', 'message'];

    public function handle(Feature $feature): int
    {
        $features = $feature->getFeatures();

        if ($features->isEmpty()) {
            $this->error("The application doesn't have any registered features.");

            return self::FAILURE;
        }

        $features = $this->filterFeatures($features);

        if ($features->isEmpty()) {
            $this->error("The application doesn't have any features matching the given criteria.");

            return self::FAILURE;
        }

        $this->table(
            $headers = $this->getHeaders(),
            $features->map(fn ($feature) => array_values($feature->only($headers))),
            'box',
        );

        return self::SUCCESS;
    }

    private function filterFeatures(Collection $features): Collection
    {
        if ($scope = $this->option('scope')) {
            $features = $features->where('scope', $scope);
        }

        if ($this->option('disabled')) {
            $features = $features->whereNotNull('disabled_at');
        } elseif ($this->option('enabled')) {
            $features = $features->whereNull('disabled_at');
        }

        return $features;
    }

    private function getHeaders(): array
    {
        if ($this->option('compact')) {
            return array_slice($this->headers, 0, count($this->headers) >> 1);
        }

        return $this->headers;
    }
}
