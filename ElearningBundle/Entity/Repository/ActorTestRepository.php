<?php

namespace ElearningBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


/**
 * Class ActorTestRepository
 */
class ActorTestRepository extends EntityRepository
{
    /**
     * Count the total of rows
     *
     * @return int
     */
    public function countTotal()
    {
        $qb = $this->getQueryBuilder()
            ->select('COUNT(l)');

        return $qb->getQuery()->getSingleScalarResult();
    }
    
    
    
    
    /**
     * Find all rows filtered for DataTables
     *
     * @param string $search        The search string
     * @param int    $sortColumn    The column to sort by
     * @param string $sortDirection The direction to sort the column
     *
     * @return \Doctrine\ORM\Query
     */
    public function findAllForDataTablesByRelation($search, $sortColumn, $sortDirection, $entityId)
    {
        // select
        $qb = $this->getQueryBuilder()
             ->select('l.id, l.evaluateDate evaluateDate, a.id actorId, a.email email')
                ;

        // join
        $qb->leftJoin('l.actor', 'a')   
           ->leftJoin('l.test', 't')   
                ;
                
        $qb->where('t.id = :test')
           ->setParameter(':test', $entityId);
            
                
        // search
        if (!empty($search)) {
            $searchArr = explode(' ', $search);
            
            foreach ($searchArr as $value) {
                $qb->andWhere('a.name LIKE :search')
                ->orWhere('a.surnames LIKE :search')
                ->setParameter('search', '%'.$value.'%');
            }
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('l.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('l.evaluateDate', $sortDirection);
                break;
            case 2:
                $qb->orderBy('a.email', $sortDirection);
                break;
            
        }

        return $qb->getQuery();
    }
    
    /**
     * Find all rows filtered for DataTables
     *
     * @param string $search        The search string
     * @param int    $sortColumn    The column to sort by
     * @param string $sortDirection The direction to sort the column
     *
     * @return \Doctrine\ORM\Query
     */
    public function findAllForDataTables($search, $sortColumn, $sortDirection)
    {
        // select
        $qb = $this->getQueryBuilder()
             ->select('l.id, l.evaluateDate evaluateDate, a.id actorId, a.email email')
                ;

        // join
        $qb->leftJoin('l.actor', 'a')   
           ->leftJoin('l.test', 't')   
                ;
                
//        $qb->where('r.role = :role')
//           ->setParameter(':role', 'ROLE_USER');
            
                
        // search
        if (!empty($search)) {
            $searchArr = explode(' ', $search);
            
            foreach ($searchArr as $value) {
                $qb->andWhere('a.name LIKE :search')
                ->orWhere('a.surnames LIKE :search')
                ->setParameter('search', '%'.$value.'%');
            }
            // where('u.email LIKE :search')
//            $qb->andWhere('u.name LIKE :search')
//                ->orWhere('u.surnames LIKE :search')
//                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('l.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('l.evaluateDate', $sortDirection);
                break;
            case 2:
                $qb->orderBy('a.email', $sortDirection);
                break;
            
        }

        return $qb->getQuery();
    }
    
    
    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('ElearningBundle:ActorTest')
            ->createQueryBuilder('l');

        return $qb;
    }
}
