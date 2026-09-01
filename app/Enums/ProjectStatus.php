<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case Planning = 'planning';
    case InAnalysis = 'in_analysis';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case Paused = 'paused';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
