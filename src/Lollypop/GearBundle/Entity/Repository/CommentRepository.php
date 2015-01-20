<?php

namespace Lollypop\GearBundle\Entity\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommentRepository
 *
 * @author seshachalam
 */
use Doctrine\ORM\EntityRepository;
use Lollypop\GearBundle\Entity\Message;
use Lollypop\GearBundle\Entity\Comment;

class CommentRepository extends EntityRepository {

    public function getCommentsOf(Message $message, $result = false) {
        $query = $this->createQueryBuilder('c')
                ->select()
                ->where('c.message = :message')
                ->setParameter('message', $message)
                ->getQuery();
        if ($result) {
            return $query->getResult();
        } else {
            return $query;
        }
    }

    public function getCommentsAfter(Message $message, Comment $comment = null, $direction = 0) {
        $crt = array('message' => $message);

        $qb = $this->createQueryBuilder('c')
                ->select()
                ->where('c.message = :message');

//direction false is down / true is up
        if (!(bool) $direction) {
            if ($comment !== null) {
                $qb->andWhere('c.id < :cid');
                $crt['cid'] = $comment->getId();
            }
        } else {
            if ($comment !== null) {
                $qb->andWhere('c.id > :cid');
                $crt['cid'] = $comment->getId();
            }
        }

        $qb->setParameters($crt);
        $qb->addOrderBy('c.id', 'DESC');

        $query = $qb->getQuery();
        $query->setMaxResults(Comment::MAX_RESULTS);

        return $query->getResult();
    }

}
