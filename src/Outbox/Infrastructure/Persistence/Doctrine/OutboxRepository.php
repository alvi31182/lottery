<?php

declare(strict_types=1);

namespace App\Outbox\Infrastructure\Persistence\Doctrine;

use App\Outbox\Model\Outbox;
use App\Outbox\Model\WriteOutboxStorage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @template-extends EntityRepository<Outbox>
 */
final class OutboxRepository extends EntityRepository implements WriteOutboxStorage
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(em: $em, class: $em->getClassMetadata(Outbox::class));
    }

    public function createOutbox(Outbox $outbox): void
    {

            $this->getEntityManager()->persist($outbox);
            $this->getEntityManager()->flush();
    }
}