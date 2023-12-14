<?php

declare(strict_types=1);

namespace App\Lottery\Persistence\Doctrine;

use App\Lottery\Model\Lottery;
use App\Lottery\Model\ReadLotteryStorage;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @psalm-suppress MissingTemplateParam
 * @method Lottery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lottery|null findOneBy(array $criteria, ?array $orderBy = null)
 */
final class LotteryRepository extends EntityRepository implements ReadLotteryStorage
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Lottery::class));
    }

    /**
     * @throws Exception
     */
    public function getByPlayerWithGameId(string $playerId): bool
    {
        $connection = $this->getEntityManager()->getConnection();

        $SQL = <<<SQL
            SELECT player_id, game_id 
                FROM lottery WHERE player_id = :playerId
SQL;
        $stmt = $connection->executeQuery(
            sql: $SQL,
            params: [
                'playerId' => $playerId
            ]
        );

        $result = $stmt->fetchAllAssociative();

        if (!empty($result)) {
            return  true;
        }

        return false;
    }
}
