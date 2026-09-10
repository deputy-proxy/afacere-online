<?php

namespace App\Enums;

enum BusinessStage: string
{
    case Idea = 'idea';
    case Validation = 'validation';
    case Launch = 'launch';
    case EarlyOperations = 'early_operations';
    case Growth = 'growth';
    case StableBusiness = 'stable_business';
    case TransformationExit = 'transformation_exit';

    /** @return list<self> */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Idea => [self::Validation],
            self::Validation => [self::Launch, self::Idea],
            self::Launch => [self::EarlyOperations, self::Validation],
            self::EarlyOperations => [self::Growth, self::Launch],
            self::Growth => [self::StableBusiness, self::EarlyOperations],
            self::StableBusiness => [self::TransformationExit, self::Growth],
            self::TransformationExit => [self::StableBusiness],
        };
    }
}
