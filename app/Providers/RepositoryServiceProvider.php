<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
/***----------Lead --------***/
use App\Interfaces\LeadRepositoryInterface;
use App\Repositories\LeadRepository;

/***----------Deal Stages --------***/
use App\Interfaces\Deal_stage_repository_Interface;
use App\Repositories\Deal_stageRepository;
/***----------Deal  --------***/
use App\Interfaces\DealRepositoryInterface;
use App\Repositories\DealRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(Deal_stage_repository_Interface::class, Deal_stageRepository::class);
        $this->app->bind(DealRepositoryInterface::class, DealRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
