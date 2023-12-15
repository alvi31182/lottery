<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Persistence\Doctrine;

use App\Lottery\Model\LotteryAward;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method LotteryAward|null find($id, $lockMode = null, $lockVersion = null)
 * @method LotteryAward|null findOneBy(array $criteria, array $orderBy = null)
 *
 * @template-extends EntityRepository<LotteryAward>
 */
final class LotteryAwardRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(LotteryAward::class));
    }
}
