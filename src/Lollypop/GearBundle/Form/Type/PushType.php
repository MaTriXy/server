<?php

namespace Lollypop\GearBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PushType extends AbstractType {
    public function getName() {
        return 'push_form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('to', 'text', array('required' => false))
                ->add('exclude', 'text', array('required' => false))
                ->add('board', 'text', array('required' => false))
                ->add('data', 'text')
                ->add('collapse_key', 'text')
                ->add('delay_while_idle', 'choice', array('choices' => array(1 => 'Yes', 0 => 'No')))
                ->add('package', 'text')
                ->add('ttl', 'integer')
                ->add('submit', 'submit');
    }

}
