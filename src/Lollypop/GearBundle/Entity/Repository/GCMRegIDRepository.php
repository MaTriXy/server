<?php

namespace Lollypop\GearBundle\Entity\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMRegIDRepository
 *
 * @author seshachalam
 */
use Doctrine\ORM\EntityRepository;
use Lollypop\GearBundle\Entity\Board;

class GCMRegIDRepository extends EntityRepository {

    public function getRegIdsOfBoard(Board $board, array $excludeValues = [], $page, $limit = 1000) {
        $qb = $this->createQueryBuilder('g')
                ->select('g.value')
                ->where('g.board = :board');
        if (!empty($excludeValues)) {
            $qb->andWhere('g.value NOT IN (:exclude_values)');
        }

        $start = ($page - 1) * $limit;

        $qb->setFirstResult($start)
                ->setMaxResults($limit);
        if (!empty($excludeValues)) {
            $qb->setParameter('exclude_values', $excludeValues);
        }
        $qb->setParameter('board', $board);

        return $qb->getQuery()->getArrayResult();
    }

}
