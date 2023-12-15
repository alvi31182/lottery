<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Persistence\Doctrine;

use App\Lottery\Application\Dto\LotteryList;
use App\Lottery\Model\Lottery;
use App\Lottery\Model\ReadLotteryStorage;
use App\Lottery\Model\WriteLotteryStorage;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @method Lottery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lottery|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Lottery|null findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends EntityRepository<Lottery>
 */
class LotteryRepository extends EntityRepository implements ReadLotteryStorage, WriteLotteryStorage
{
    public function __construct(
        EntityManagerInterface $em,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($em, $em->getClassMetadata(className: Lottery::class));
    }

    public function findByPlayerWithGameId(string $playerId, string $gameId): ?Lottery
    {
        return $this->findOneBy(criteria: ['playerId' => $playerId, 'gameId' => $gameId]);
    }

    public function createLottery(Lottery $lottery): void
    {
        try {
            $this->getEntityManager()->persist($lottery);
            $this->getEntityManager()->flush();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     * @return array<LotteryList>
     */
    public function getLotteryList(): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $lotteryDtoList = [];

        $SQL = <<<SQL
            SELECT 
                lott.game_id, 
                lott.player_id, 
                lott.stake
            FROM lottery AS lott WHERE lott.updated_at IS NULL
SQL;
        $results = $connection->executeQuery(
            sql: $SQL
        )->fetchAllAssociative();

        foreach ($results as $result) {
            $lotteryDtoList[] = new LotteryList(
                gameId: $result['game_id'],
                playerId: $result['player_id'],
                stake: $result['stake']
            );
        }

        return $lotteryDtoList;
    }
}
