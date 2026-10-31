<?php

namespace App\Providers;

use App\Domain\Contracts\WorkRepositoryInterface;
use App\Infrastructure\Persistence\EloquentWorkRepository;
use App\Models\Branch;
use App\Models\Work;
use App\Policies\BranchPolicy;
use App\Policies\LoanPolicy;
use App\Policies\TransferPolicy;
use App\Policies\WorkPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WorkRepositoryInterface::class, EloquentWorkRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Work::class, WorkPolicy::class);
        Gate::policy(Branch::class, BranchPolicy::class);

        // Named Gates for Domain Capabilities
        Gate::define('manage-catalog', fn ($user) => (new WorkPolicy)->create($user));
        Gate::define('manage-branches', fn ($user) => (new BranchPolicy)->create($user));
        Gate::define('issue-loan', fn ($user, $branchId) => (new LoanPolicy)->issueLoan($user, $branchId));
        Gate::define('return-loan', fn ($user, $branchId) => (new LoanPolicy)->returnLoan($user, $branchId));
        Gate::define('manage-transfers', fn ($user) => (new TransferPolicy)->markAsLost($user));
    }
}
