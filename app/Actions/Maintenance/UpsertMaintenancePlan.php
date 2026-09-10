<?php

namespace App\Actions\Maintenance;

use App\Enums\BillingCycle;
use App\Models\MaintenancePlan;
use Carbon\CarbonImmutable;

class UpsertMaintenancePlan
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?MaintenancePlan $plan = null): MaintenancePlan
    {
        $plan ??= new MaintenancePlan;
        $plan->fill($data);

        $plan->next_renewal_on = $this->nextRenewal(
            $plan->billing_cycle,
            CarbonImmutable::parse($plan->starts_on),
        );

        $plan->save();

        return $plan->refresh();
    }

    private function nextRenewal(BillingCycle $cycle, CarbonImmutable $start): ?CarbonImmutable
    {
        $months = $cycle->months();

        if ($months === null) {
            return null;
        }

        $next = $start;
        $today = CarbonImmutable::now()->startOfDay();

        while ($next->lte($today)) {
            $next = $next->addMonths($months);
        }

        return $next;
    }
}
