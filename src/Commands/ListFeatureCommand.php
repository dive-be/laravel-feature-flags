<?php

namespace Dive\FeatureFlags\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

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

    public function handle(Application $app)
    {
        // noop
    }
}
