<?php

declare(strict_types=1);

namespace ContaoGraveyard\StylePickerBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class GetStylePickerFilterEvent extends Event
{
    private array|int|string|null $layout = null;

    private bool|string|null $section = null;

    private bool|string|null $condition = null;

    public function __construct(
        private readonly string $table,
        private readonly int $id,
    ) {
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLayout(): array|int|string|null
    {
        return $this->layout;
    }

    public function setLayout(array|int|string|null $layout): void
    {
        $this->layout = $layout;
    }

    public function getSection(): bool|string|null
    {
        return $this->section;
    }

    public function setSection(bool|string|null $section): void
    {
        $this->section = $section;
    }

    public function getCondition(): bool|string|null
    {
        return $this->condition;
    }

    public function setCondition(bool|string|null $condition): void
    {
        $this->condition = $condition;
    }
}
