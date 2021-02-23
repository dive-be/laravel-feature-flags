<?php

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

    protected array $headers = ['state', 'scope', 'name', 'label', 'description', 'message'];

    public function handle(Feature $feature)
    {
        $features = $feature->getFeatures();

        if ($features->isEmpty()) {
            $this->error("The application doesn't have any registered features.");

            return 1;
        }

        $features = $this->filterFeatures($features);

        if ($features->isEmpty()) {
            $this->error("The application doesn't have any features matching the given criteria.");

            return 1;
        }

        $this->renderFeatures($features);
    }

    private function filterFeatures(Collection $features): Collection
    {
        foreach (['scope', 'enabled', 'disabled'] as $option) {
            if ($value = $this->option($option)) {
                $features = $features->where($option, $value);
            }
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

    private function renderFeatures(Collection $features)
    {
        $this->table($this->getHeaders(), $features);
    }
}
