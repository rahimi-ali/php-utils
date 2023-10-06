<?php

declare(strict_types=1);

/** @template T of mixed */
abstract class FakeFactory
{
    /** @var array<string, mixed> */
    private array $overrides = [];

    public static function new(): static
    {
        return new static();
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function definition(): array;

    /**
     * @param array<string, mixed> $properties
     * @return T
     */
    abstract protected function make(array $properties): mixed;

    /**
     * @param array<string, mixed> $overrides
     * @return static
     */
    public function override(array $overrides): static
    {
        $new = clone $this;

        $new->overrides = [...$this->overrides, ...$overrides];

        return $new;
    }

    /** @return T */
    public function makeOne(): object
    {
        $definition = $this->definition();

        $definition = $this->applyOverrides($definition, $this->overrides);

        return $this->make($this->materializeDefinition($definition));
    }

    /** @return T[] */
    public function makeMany(int $count): array
    {
        $definition = $this->definition();

        $definition = $this->applyOverrides($definition, $this->overrides);

        $objects = [];
        for ($i = 0; $i < $count; $i++) {
            $objects = $this->make($this->materializeDefinition($definition));
        }

        return $objects;
    }

    /**
     * @param array<string, mixed> $definition
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function applyOverrides(array $definition, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            if (is_callable($value)) {
                $value = $value($definition);
            }

            $this->setNestedValue($definition, $key, $value);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $definition
     * @return array<string, mixed>
     */
    private function materializeDefinition(array $definition): array
    {
        $materialized = [];

        foreach ($definition as $key => $value) {
            if (is_callable($value)) {
                $materialized[$key] = $value($this->faker);
            } elseif ($value instanceof FakeFactory) {
                $materialized[$key] = $value->makeOne();
            } else {
                $materialized[$key] = $value;
            }
        }

        return $materialized;
    }

    /**
     * @param array<string, mixed> $definition
     */
    private function setNestedValue(array &$definition, string $key, mixed $value): void
    {
        $keys = explode('.', $key);

        $current = &$definition;

        foreach ($keys as $key) {
            $current = &$current[$key];
        }

        $current = $value;
    }
}