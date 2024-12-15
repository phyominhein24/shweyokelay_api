<?php

namespace App\Providers;

use App\Helpers\QueryBuilderHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerBlueprintMacros();
        $this->registerQueryBuilderMacros();
    }

    private function registerBlueprintMacros()
    {
        Blueprint::macro('snowflakeId', fn ($column = 'id') => $this->unsignedBigInteger($column));
        Blueprint::macro('snowflakeIdAndPrimary', fn ($column = 'id') => $this->snowflakeId($column)->primary());

        Blueprint::macro('auditColumns', function () {
            $this->snowflakeId('created_by')->nullable();
            $this->snowflakeId('updated_by')->nullable();
            $this->snowflakeId('deleted_by')->nullable();
            $this->timestamps();
            $this->softDeletes();

            return $this;
        });
    }

    private function registerQueryBuilderMacros()
    {
        Builder::macro('sortingQuery', function () {
            return QueryBuilderHelper::sortingQuery($this);
        });

        Builder::macro('searchQuery', function () {
            return QueryBuilderHelper::searchQuery($this);
        });

        Builder::macro('paginationQuery', function () {
            return QueryBuilderHelper::paginationQuery($this);
        });

        Builder::macro('filterQuery', function () {
            return QueryBuilderHelper::filterQuery($this);
        });

        Builder::macro('filterDateQuery', function () {
            return QueryBuilderHelper::filterDateQuery($this);
        });
    }
}
