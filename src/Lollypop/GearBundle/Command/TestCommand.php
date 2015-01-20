<?php

namespace Lollypop\GearBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Lollypop\GearBundle\Entity\Comment;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestCommand
 *
 * @author seshachalam
 */
class TestCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('board:check')
                ->setDescription('number of reg ids')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $messageRepo = $em->getRepository('LollypopGearBundle:Message');
        $message = $messageRepo->findOneBy(array('uri' => '05a2c6ac-e992-4b78-887d-bae24b9e740a'));
        if ($message) {
            $comment = new Comment();
            $comment->setMessage($message)->setContent("Test")->setUri(uniqid('test'))->setWieght(0);
            $em->persist($comment);
            $message->addComment($comment);
            $output->writeln($message->getContent());
        }
        $em->flush();
    }

}
