<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Fixer\CommentLengthFixer;
use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Basic\PsrAutoloadingFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return ECSConfig::configure()
    ->withPreparedSets(psr12: true, common: true, symplify: true)
    ->withSets([SetList::CONTAO])
    ->withPaths([
         __DIR__ . '/contao',
         __DIR__ . '/src',
    ])
    ->withRules([
        NoUnusedImportsFixer::class,
    ])
    ->withSkip([
        '*/*/languages/*',
        CommentLengthFixer::class,
        MethodChainingIndentationFixer::class,
        PhpdocSeparationFixer::class,
        DisallowArrayTypeHintSyntaxSniff::class,
        LineLengthFixer::class,
        NotOperatorWithSuccessorSpaceFixer::class,
        PsrAutoloadingFixer::class,
        MethodChainingNewlineFixer::class,
    ])
    ->withParallel()
    ->withSpacing(Option::INDENTATION_SPACES, "\n")
;
