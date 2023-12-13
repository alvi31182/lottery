<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Lottery\Model\Lottery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @template-extends  EntityRepository<LotteryRepository>
 */
final class LotteryRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Lottery::class));
    }
}
