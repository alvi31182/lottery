<?php

declare(strict_types=1);

namespace App\Outbox\Infrastructure\Persistence\Doctrine;

use App\Outbox\Model\Outbox;
use App\Outbox\Model\ReadOutboxStorage;
use App\Outbox\Model\WriteOutboxStorage;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Traversable;

/**
 * @template-extends EntityRepository<Outbox>
 */
final class OutboxRepository extends EntityRepository implements WriteOutboxStorage, ReadOutboxStorage
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

    /**
     * @return Traversable<int,array<string,mixed>>
     *
     * @throws Exception
     *
     */
    public function getNotSendOutboxData(): iterable
    {
        $connection = $this->getEntityManager()->getConnection();

        $SQL = <<<SQL
            SELECT event_data FROM outbox WHERE is_send = false
SQL;

        return $connection->executeQuery(
            sql: $SQL
        )->iterateAssociative();
    }
}
