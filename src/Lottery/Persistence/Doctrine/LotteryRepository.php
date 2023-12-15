<?php

declare(strict_types=1);

namespace App\Lottery\Persistence\Doctrine;

use App\Lottery\Model\Lottery;
use App\Lottery\Model\ReadLotteryStorage;
use App\Lottery\Model\WriteLotteryStorage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @psalm-suppress MissingTemplateParam
 * @method Lottery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lottery|null findOneBy(array $criteria, ?array $orderBy = null)
 */
final class LotteryRepository extends EntityRepository implements ReadLotteryStorage, WriteLotteryStorage
{
    public function __construct(EntityManagerInterface $em, private LoggerInterface $logger)
    {
        parent::__construct($em, $em->getClassMetadata(Lottery::class));
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
}
