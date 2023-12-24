<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Persistence\Doctrine;

use App\Lottery\Application\Dto\LotteryListInStarted;
use App\Lottery\Application\Dto\LotteryListInWaiting;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

final readonly class NativeSqlQueryForLotteryTable
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @return array<LotteryListInWaiting>
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLotteryListByStatusInWaiting(): array
    {
        $connection = $this->entityManager->getConnection();

        $lotteryDtoList = [];

        $SQL = <<<SQL
            SELECT 
                lott.game_id, 
                lott.player_id
            FROM lottery AS lott WHERE status = 'in_waiting' 
                                   AND lott.updated_at IS NULL
SQL;
        $results = $connection->executeQuery(
            sql: $SQL
        )->fetchAllAssociative();

        foreach ($results as $result) {
            $lotteryDtoList[] = new LotteryListInWaiting(
                gameId: $result['game_id'],
                playerId: $result['player_id']
            );
        }

        return $lotteryDtoList;
    }

    /**
     * @return array<LotteryListInStarted>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLotteryListInStarted(): array
    {
        $connection = $this->entityManager->getConnection();

        $lotteryStartedDtoList = [];

        $SQL = <<<SQL
            SELECT
                lott.id,
                lott.game_id, 
                lott.player_id,
                lott.stake
            FROM lottery AS lott WHERE status = 'started' 
                                   AND lott.updated_at IS NULL
SQL;

        $results = $connection->executeQuery(
            sql: $SQL
        )->fetchAllAssociative();

        foreach ($results as $result) {
            $lotteryStartedDtoList[] = new LotteryListInStarted(
                lotteryId: $result['id'],
                gameId: $result['game_id'],
                playerId: $result['player_id'],
                stake: $result['stake']
            );
        }

        return $lotteryStartedDtoList;
    }

    /**
     * @param array<LotteryListInWaiting> $lotteryListWaiting
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function toStarted(array $lotteryListWaiting): void
    {
        $connection = $this->entityManager->getConnection();

        try {
            $connection->beginTransaction();

            $SQL = $this->sqlBuildToStarted($lotteryListWaiting);

            $parameters = $this->extractPlayerIdsForInWaiting($lotteryListWaiting);

            $connection->executeStatement($SQL, $parameters);

            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            $this->logger->error($e->getMessage());
        }
    }

    public function toFinished(string $lotteryId): void
    {
        $connection = $this->entityManager->getConnection();

        try {
            $connection->beginTransaction();

            $SQL = $this->sqlBuildToFinished();

            $connection->executeStatement(
                sql: $SQL,
                params: [
                    'lotteryId' => $lotteryId,
                ]
            );

            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            $this->logger->error($e->getMessage());
        }
    }

    private function sqlBuildToFinished(): string
    {
        return <<<SQL
            UPDATE lottery
            SET status = CASE 
                WHEN id = (:lotteryId) THEN 'winner'
                ELSE 'finished'
            END
            WHERE id <> :lotteryId
SQL;
    }

    /**
     * @param array<LotteryListInWaiting> $lotteryListWaiting
     */
    private function sqlBuildToStarted(array $lotteryListWaiting): string
    {
        $placeholders = implode(',', array_fill(0, count($lotteryListWaiting), '?'));

        return <<<SQL
                UPDATE lottery
                    SET status = 'started'
                WHERE player_id IN ($placeholders) AND status = 'in_waiting'
SQL;
    }

    /**
     * @param array<LotteryListInWaiting> $lotteryListWaiting
     *
     * @return array<string>
     */
    private function extractPlayerIdsForInWaiting(array $lotteryListWaiting): array
    {
        return array_map(static fn($lottery) => $lottery->playerId, $lotteryListWaiting);
    }
}
