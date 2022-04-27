<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\DTO\Request\VerificationSubjectDTO;
use App\Entity\Verification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Verification find($id, $lockMode = null, $lockVersion = null)
 * @method null|Verification findOneBy(array $criteria, array $orderBy = null)
 * @method Verification[]    findAll()
 * @method Verification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Verification::class);
    }

    /**
     * @throws Exception
     */
    public function findIdsBySubject(VerificationSubjectDTO $subject): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->executeQuery(
            <<<'SQL'
                 SELECT id
                 FROM verification
                 WHERE JSON_EXTRACT(subject, "$.type") LIKE ?
                 AND JSON_EXTRACT(subject, "$.identity") LIKE ?
            SQL,
            [
                '%'.$subject->getType().'%',
                '%'.$subject->getIdentity().'%',
            ]
        );

        return $statement->fetchAllAssociative();
    }
}
