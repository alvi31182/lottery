<?php

declare(strict_types=1);

namespace App\Outbox\Infrastructure\Persistence\Doctrine;

use App\Outbox\Application\Dto\OutboxList;
use App\Outbox\Model\Outbox;
use App\Outbox\Model\ReadOutboxStorage;
use App\Outbox\Model\WriteOutboxStorage;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @template-extends EntityRepository<Outbox>
 */
final class OutboxRepository extends EntityRepository implements WriteOutboxStorage, ReadOutboxStorage
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(em: $em, class: $em->getClassMetadata(Outbox::class));
    }

    public function findById(string $id): ?Outbox
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function createOutbox(Outbox $outbox): void
    {
        $this->getEntityManager()->persist($outbox);
        $this->getEntityManager()->flush();
    }

    public function statusSendToTrue(Outbox $outbox): void
    {
        $this->getEntityManager()->persist($outbox);
        $this->getEntityManager()->flush();
    }

    /**
     * @return iterable<OutboxList>
     *
     * @throws Exception
     *
     */
    public function getNotSendOutboxData(): iterable
    {
        $connection = $this->getEntityManager()->getConnection();

        $SQL = <<<SQL
            SELECT id, 
                   is_send, 
                   event_data 
            FROM outbox WHERE is_send = false
SQL;

        $results = $connection->executeQuery(
            sql: $SQL
        )->iterateAssociative();

        foreach ($results as $result) {
            yield new OutboxList(
                outboxId: $result['id'],
                isSend: $result['is_send'],
                eventData: $result['event_data']
            );
        }
    }
}
