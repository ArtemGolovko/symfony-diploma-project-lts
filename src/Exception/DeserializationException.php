<?php

namespace App\Exception;

final class DeserializationException extends \Exception
{
    /**
     * @var array
     */
    private array $violations;

    /**
     * @param string $massage
     * @param array  $violations
     */
    public function __construct(string $massage, array $violations)
    {
        parent::__construct($massage);
        $this->violations = $violations;
    }

    /**
     * @return array
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
