<?php

namespace App\Enums;

enum EvaluationStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Archived = 'archived';
}
