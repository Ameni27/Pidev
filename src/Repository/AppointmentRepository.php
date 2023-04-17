<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 *
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function save(Appointment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Appointment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // App\Repository\AppointmentRepository.php

    public function findBySearchCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('a');

        if (isset($criteria['patientName'])) {
            $qb->andWhere('a.patientName LIKE :patientName')
                ->setParameter('patientName', '%' . $criteria['patientName'] . '%');
        }

        if (isset($criteria['appointmentDate'])) {
            $qb->andWhere('a.dateap = :appointmentDate')
                ->setParameter('appointmentDate', $criteria['appointmentDate']);
        }

        if (isset($criteria['appointmentTime'])) {
            $qb->andWhere('a.hour = :appointmentTime')
                ->setParameter('appointmentTime', $criteria['appointmentTime']);
        }
        if (isset($criteria['status'])) {
            if ($criteria['status'] === null) {
                $qb->andWhere('a.status IS NULL');
            } elseif ($criteria['status'] === true) {
                $qb->andWhere('a.status = true');
            } elseif ($criteria['status'] === false) {
                $qb->andWhere('a.status = false');
            }
        }
    

        return $qb->getQuery()->getResult();
    }



//    /**
//     * @return Appointment[] Returns an array of Appointment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Appointment
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
