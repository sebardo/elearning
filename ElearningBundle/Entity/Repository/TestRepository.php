<?php

namespace ElearningBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use CoreBundle\Entity\Site;


/**
 * Class TestRepository
 */
class TestRepository extends EntityRepository
{
    /**
     * Count the total of rows
     *
     * @return int
     */
    public function countTotal()
    {
        $qb = $this->getQueryBuilder()
            ->select('COUNT(t)');

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
            ->select('t.id, t.name, t.active, co.name as course, s.name site')
            ->leftJoin('t.course', 'co')
            ->leftJoin('co.site', 's');

        // search
        if (!empty($search)) {
            $qb->where('t.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('t.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('t.name', $sortDirection);
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
    public function findAllForDataTablesBySites($search, $sortColumn, $sortDirection, $siteIds)
    {
        // select
        $qb = $this->getQueryBuilder()
             ->select('t.id, t.name, t.active, co.name as course, s.name site')
            ->leftJoin('t.course', 'co')
            ->leftJoin('co.site', 's'); 
        
        
        // search
        if (!empty($search)) {
            $qb->where('t.name LIKE :search')
                 ->andWhere('s.id IN(:siteIds)')
                ->setParameter('search', '%'.$search.'%');
        }else{
            $qb->where('s.id IN(:siteIds)');
        }

        $qb->setParameter('siteIds', array_values($siteIds));
        
        switch($sortColumn) {
            case 0:
                $qb->orderBy('t.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('t.name', $sortDirection);
                break;
        }

        return $qb->getQuery();
    } 

    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('ElearningBundle:Test')
            ->createQueryBuilder('t');

        return $qb;
    }
}