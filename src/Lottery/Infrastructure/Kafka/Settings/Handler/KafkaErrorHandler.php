<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Kafka\Settings\Handler;

use Psr\Log\LoggerInterface;

final readonly class KafkaErrorHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @psalm-suppress UndefinedFunction
     * @psalm-suppress UndefinedConstant
     */
    public function handleError(mixed $error, string $reason): void
    {
        if ($error === RD_KAFKA_RESP_ERR__FATAL) {
            $this->logger->critical(
                message: "KAFKA FATAL ERROR",
                context: [
                    "message" => sprintf("Error %d %s. Reason: %s", $error, rd_kafka_err2str($error), $reason),
                ],
            );
        }
    }
}
