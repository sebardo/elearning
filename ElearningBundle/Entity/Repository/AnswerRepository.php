<?php

namespace ElearningBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use CoreBundle\Entity\Site;


/**
 * Class AnswerRepository
 */
class AnswerRepository extends EntityRepository
{
    /**
     * Count the total of rows
     *
     * @return int
     */
    public function countTotal()
    {
        $qb = $this->getQueryBuilder()
            ->select('COUNT(a)');

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
    public function findAllForDataTables($search, $sortColumn, $sortDirection)
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('a.id, a.name, a.active, co.name as course, s.name site')
            ->leftJoin('a.ask', 'ask')
            ->leftJoin('ask.test', 't')
            ->leftJoin('t.course', 'co')
            ->leftJoin('co.site', 's');

        // search
        if (!empty($search)) {
            $qb->where('a.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('a.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('a.name', $sortDirection);
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
    public function findAllForDataTablesBySite($search, $sortColumn, $sortDirection, $siteId)
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('a.id, a.name, co.name as course, s.name site')
            ->leftJoin('a.ask', 'ask')
            ->leftJoin('ask.test', 't')
            ->leftJoin('t.course', 'co')
            ->leftJoin('co.site', 's'); 
        
        
        // search
        if (!empty($search)) {
            $qb->where('a.name LIKE :search')
                ->andWhere('s.id = :siteId')
                ->setParameter('search', '%'.$search.'%');
        }else{
            $qb->where('co.id = :courseId');
        }

        $qb->setParameter('siteId', $siteId);
        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('a.name', $sortDirection);
                break;
        }

//        $qb->groupBy('u.username');
        
        return $qb->getQuery();
    } 

    
    public function getNextId(){
        $qb = $this->getQueryBuilder()
            ->select('a.id')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(1);
                
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('ElearningBundle:Answer')
            ->createQueryBuilder('a');

        return $qb;
    }
}