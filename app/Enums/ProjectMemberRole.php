<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectMemberRole: string
{
    case ProductOwner = 'product_owner';
    case TechLead = 'tech_lead';
    case Developer = 'developer';
    case Designer = 'designer';
    case Stakeholder = 'stakeholder';
}
