<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @extends ServiceEntityRepository<Cours>
 *
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    public function save(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

/*
    public function salleIsDisponibleAtThisMoment($salle,$dateDeb,$dateFin): int
   {
        $qb = $this->createQueryBuilder('c');
        $cours = $qb->select('c.id')
                    ->where(
                        $qb->expr()->andX(
                        $qb->expr()->orX(
                            $qb->expr()->andX(
                                $qb->expr()->lt('c.dateHeureDebut', ':start_date'),
                                $qb->expr()->lt(':start_date', 'c.dateHeureFin')
                            ),
                            $qb->expr()->andX(
                                $qb->expr()->gt('c.dateHeureDebut', ':start_date'),
                                $qb->expr()->lt(':end_date', 'c.dateHeureDebut')
                            )
                        ),
                        $qb->expr()->eq('c.salle', ':salle')
                            )
                    )
                    ->setParameter('start_date', $dateDeb)
                    ->setParameter('end_date', $dateFin)
                    ->setParameter('salle', $salle)
                        ->getQuery()
                        ->getResult();

        
        $nbCours = count($cours);
        return $nbCours;
   }
*/
   /**
    * @return Cours[] Returns an array of Cours objects
    */
   public function getByDate($date): array
   {
    $startDate = clone $date;
    $startDate->setTime(0, 0, 0);

    $endDate = clone $date;
    $endDate->setTime(23, 59, 59);

       return $this->createQueryBuilder('c')
       ->andWhere('c.dateHeureDebut BETWEEN :start_date AND :end_date')
       ->setParameter('start_date', $startDate)
       ->setParameter('end_date', $endDate)
       ->orderBy('c.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?Cours
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
