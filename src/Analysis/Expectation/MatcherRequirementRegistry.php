<?php

declare(strict_types=1);

namespace Pest\PHPStan\Analysis\Expectation;

final class MatcherRequirementRegistry
{
    public const string STRING = 'string';

    public const string ITERABLE = 'iterable';

    public const string COUNTABLE_OR_ITERABLE = 'countable_or_iterable';

    /** @var array<string, string> */
    private const array METHOD_REQUIREMENTS = [
        'json' => self::STRING,
        'toStartWith' => self::STRING,
        'toEndWith' => self::STRING,
        'toBeJson' => self::STRING,
        'toBeUppercase' => self::STRING,
        'toBeLowercase' => self::STRING,
        'toBeAlphaNumeric' => self::STRING,
        'toBeAlpha' => self::STRING,
        'toBeDigits' => self::STRING,
        'toBeSnakeCase' => self::STRING,
        'toBeKebabCase' => self::STRING,
        'toBeCamelCase' => self::STRING,
        'toBeStudlyCase' => self::STRING,
        'toBeUuid' => self::STRING,
        'toBeUrl' => self::STRING,
        'toBeSlug' => self::STRING,
        'toMatch' => self::STRING,
        'toBeDirectory' => self::STRING,
        'toBeFile' => self::STRING,
        'toBeReadableFile' => self::STRING,
        'toBeWritableFile' => self::STRING,
        'toBeReadableDirectory' => self::STRING,
        'toBeWritableDirectory' => self::STRING,
        'each' => self::ITERABLE,
        'sequence' => self::ITERABLE,
        'toContainEqual' => self::ITERABLE,
        'toContainOnlyInstancesOf' => self::ITERABLE,
        'toHaveCount' => self::COUNTABLE_OR_ITERABLE,
        'toHaveSameSize' => self::COUNTABLE_OR_ITERABLE,
    ];

    public function requirementFor(string $methodName): ?string
    {
        return self::METHOD_REQUIREMENTS[$methodName] ?? null;
    }
}
