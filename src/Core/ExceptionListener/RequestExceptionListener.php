<?php

declare(strict_types=1);

namespace App\Core\ExceptionListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class RequestExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $previous = $exception->getPrevious();

        if ($previous instanceof ValidationFailedException) {
            $errors = $this->extractValidationErrors(exception: $previous);

            $event->setResponse(
                new JsonResponse(
                    data: $errors
                )
            );
        }
    }

    /**
     * @return array<string>
     */
    private function extractValidationErrors(ValidationFailedException $exception): array
    {
        $violations = $exception->getViolations();
        $errors = [];

        foreach ($violations as $violation) {
            $parameters = $violation->getParameters();

            $errors[] = (string) $violation->getMessage();

            $filterParameters = array_filter($parameters, static function ($param) {
                return is_scalar($param) || is_object($param);
            });

            $errors = array_merge($errors, array_map('strval', $filterParameters));
        }

        return $errors;
    }
}
