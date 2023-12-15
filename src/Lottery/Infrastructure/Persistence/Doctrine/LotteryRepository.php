<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Persistence\Doctrine;

use App\Lottery\Model\Lottery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method Lottery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lottery|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Lottery|null findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends EntityRepository<Lottery>
 */
class LotteryRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Lottery::class));
    }
}
