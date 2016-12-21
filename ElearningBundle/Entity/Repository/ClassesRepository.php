<?php

namespace ElearningBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use ElearningBundle\Entity\Creche;


/**
 * Class ClassesRepository
 */
class ClassesRepository extends EntityRepository
{
    /**
     * Count the total of rows
     *
     * @return int
     */
    public function countTotal()
    {
        $qb = $this->getQueryBuilder()
            ->select('COUNT(c)');

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
            ->select('c.id, c.name, co.name as course, s.name as site')
            ->leftJoin('c.course', 'co')
            ->leftJoin('co.site', 's');    


        // search
        if (!empty($search)) {
            $qb->where('c.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('c.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('c.name', $sortDirection);
                break;
        }

        return $qb->getQuery();
    }

    public function findAllCreacheClasses()
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('c.id, c.name, co.name as course_id , co.name as course_name, s.id as site_id, s.name as site_name')
            ->leftJoin('c.course', 'co')
            ->leftJoin('co.site', 's');    

        return $qb->getQuery()->getResult();;
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
    public function findAllForDataTablesByCreche($search, $sortColumn, $sortDirection, $siteId)
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('c.id, c.name, co.name as course, s.name as site')
            ->leftJoin('c.course', 'co')
            ->leftJoin('co.site', 's'); 
        
        
        // search
        if (!empty($search)) {
            $qb->where('c.name LIKE :search')
                ->andWhere('s.id = :siteId')
                ->setParameter('search', '%'.$search.'%');
        }else{
            $qb->where('s.id = :siteId');
        }

        $qb->setParameter('siteId', $siteId);
        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('c.name', $sortDirection);
                break;
        }

//        $qb->groupBy('u.username');
        
        return $qb->getQuery();
    } 
    
    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('ElearningBundle:Classes')
            ->createQueryBuilder('c');

        return $qb;
    }
}