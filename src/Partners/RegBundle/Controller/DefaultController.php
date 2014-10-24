<?php

namespace Partners\RegBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Partners\RegBundle\Form\UserType;
use Partners\RegBundle\Entity\User;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        return $this->render(
            'RegBundle:Default:index.html.twig',
            array('form' => $form->createView())
        );
    }
}
