<?php

namespace ElearningBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use CoreBundle\Entity\Site;


/**
 * Class CourseRepository
 */
class CourseRepository extends EntityRepository
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
            ->select('c.id, c.name, s.name as site')
            ->leftJoin('c.site', 's');

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
             ->select('c.id, c.name, s.name as site')
            ->leftJoin('c.site', 's'); 
        
        
        // search
        if (!empty($search)) {
            $qb->where('c.name LIKE :search')
                 ->andWhere('s.id IN(:siteIds)')
                ->setParameter('search', '%'.$search.'%');
        }else{
            $qb->where('s.id IN(:siteIds)');
        }

        $qb->setParameter('siteIds', array_values($siteIds));
        
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

    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('ElearningBundle:Course')
            ->createQueryBuilder('c');

        return $qb;
    }
}