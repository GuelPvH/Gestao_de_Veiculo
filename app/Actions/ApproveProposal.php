<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ProposalStatus;
use App\Models\Proposal;

final readonly class ApproveProposal
{
    public function handle(Proposal $proposal): Proposal
    {
        $proposal->setAttribute('status', ProposalStatus::Accepted->value);
        $proposal->setAttribute('accepted_at', now());
        $proposal->save();

        return $proposal->refresh();
    }
}
