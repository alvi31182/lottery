<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Persistence\Doctrine;

use App\Lottery\Application\Dto\LotteryListInStarted;
use App\Lottery\Application\Dto\LotteryListInWaiting;
use App\Lottery\Model\Lottery;
use App\Lottery\Model\LotteryId;
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
        private readonly NativeSqlQueryForLotteryTable $nativeSqlQuery,
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
     * @return array<LotteryListInWaiting>
     * @throws Exception
     */
    public function getLotteryListByStatusInWaiting(): array
    {
        return $this->nativeSqlQuery->getLotteryListByStatusInWaiting();
    }

    /**
     * @return array<LotteryListInStarted>
     *
     * @throws Exception
     */
    public function getLotteryListInStarted(): array
    {
        return $this->nativeSqlQuery->getLotteryListInStarted();
    }

    /**
     * @param array<LotteryListInWaiting> $lotteryListWaiting
     *
     * @throws Exception
     */
    public function updateLotteryStatusToStarted(array $lotteryListWaiting): void
    {
        $this->nativeSqlQuery->toStarted(lotteryListWaiting: $lotteryListWaiting);
    }

    public function updateLotteryStatusToFinished(LotteryId $lotteryId): void
    {
        $this->nativeSqlQuery->toFinished(lotteryId: $lotteryId->getId()->toString());
    }
}
