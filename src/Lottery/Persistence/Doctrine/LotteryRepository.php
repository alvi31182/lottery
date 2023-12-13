<?php

declare(strict_types=1);

namespace App\Lottery\Persistence\Doctrine;

use App\Lottery\Model\Lottery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class LotteryRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Lottery::class));
    }
}
