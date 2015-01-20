<?php

namespace Lollypop\GearBundle\Entity\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MessageRepository
 *
 * @author seshachalam
 */
use Doctrine\ORM\EntityRepository;
use Lollypop\GearBundle\Entity\Message;
use Lollypop\GearBundle\Entity\Board;

class MessageRepository extends EntityRepository {

    public function findNotInMessages(array $uuids) {
        $qb = $this->createQueryBuilder('m')
                ->select('m.uri');

        $qb->where($qb->expr()->in('m.uri', $uuids));
        $query = $qb->getQuery();

        return $query->getScalarResult();
    }

    public function getMessagesAfter(Board $board, Message $message = null, $direction = 0) {
        $qb = $this->createQueryBuilder('m')
                ->select()
                ->where('m.board = :board');
        $crt = array('board' => $board);

        //direction false is down / true is up
        if (!(bool) $direction) {
            if ($message !== null) {
                $qb->andWhere('m.id < :mid');
                $crt['mid'] = $message->getId();
            }
        } else {
            if ($message !== null) {
                $qb->andWhere('m.id > :mid');
                $crt['mid'] = $message->getId();
            }
        }

        $qb->setParameters($crt);
        $qb->addOrderBy('m.id', 'DESC');

        $query = $qb->getQuery();
        $query->setMaxResults(Message::MAX_RESULTS);

        return $query->getResult();
    }

    public function getLastMessage(Board $board) {
        $qb = $this->createQueryBuilder('m')
                ->select()
                ->where('m.board = :board')
                ->orderBy('id', 'DESC')
                ->setMaxResults(1)
                ->setParameter('board', $board);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getMessagesOfBoard($query, Board $board) {
        return $this->createQueryBuilder('m')
                        ->addSelect("MATCH_AGAINST (m.content, :content 'IN NATURAL MODE') as score")
                        ->add('where', 'MATCH_AGAINST(m.content, :content) > 0.8')
                        ->andWhere('m.board = :board')
                        ->setParameters(['content' => $query, 'board' => $board])
                        ->getQuery()
                        ->getResult();
    }

}
