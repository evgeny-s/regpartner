<?php

namespace Partners\RegBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Partners\RegBundle\Form\UserType;
use Partners\RegBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    protected $em;

    public function indexAction(Request $request)
    {
        $this->em = $this->getDoctrine()->getManager();

        $user = new User();
        $request->attributes->set('redirect_to', $this->generateUrl('partners_reg_homepage'));
        $form = $this->createForm(new UserType(), $user);

        $result = $this->processForm($form, $request);
        if (is_object($result)) {
            $this->get('session')->getFlashBag()->add('notice', 'Successfully!');
            return $result;
        }

        return $this->render(
            'RegBundle:Default:index.html.twig',
            array(
                'form' => $form->createView(),
                'type_user' => User::TYPE_USER,
                'type_partner' => User::TYPE_PARTNER,
                'user_only' => false
            )
        );
    }

    private function processForm($form, $request, $user_only = false) {
        $form->handleRequest($request);


        if ($form->isValid()) {
            $object = $form->getData();

            foreach($object->getUserSites() as $site) {
                $this->em->persist($site);
            }

            /* cut partner code from object */
            $partner_code = $object->getPartnerCode();
            $object->setPartnerCode(NULL);

            /* process hash password */
            $password = $object->getPassword();
            $object->setPassword(md5($password));

            $this->em->persist($object);
            $this->em->flush();

            if ($object->getType() == User::TYPE_PARTNER) {
                $this->processPartnerRegistration($object);
            } elseif($user_only) {
                $this->processUserRegistration($object, $partner_code);
            }

            if ($redirect_to = $request->attributes->get('redirect_to')) {
                return new RedirectResponse($request->attributes->get('redirect_to'));
            }
        }
    }

    private function processPartnerRegistration($object) {
        /* First - generate activate code */
        $activate_code = $this->get('reg.password_gen')->generate(20);

        /* Then - partner code */
        $partner_code = $this->get('reg.password_gen')->generate(10);
        $object->setPartnerCode($partner_code);
        $object->setConfirmCode($activate_code);
        $object->setBalance(User::START_BALANCE);

        $this->em->persist($object);
        $this->em->flush();

        if ($email = $object->getEmail()) {
            $admin_email = 'admin@regpartner.com';
            $link = $this->generateUrl('partners_reg_activation', array('code' => $activate_code), true);

            $message = \Swift_Message::newInstance()
                ->setSubject('Activation code for confirm registration')
                //TODO: send to config
                ->setFrom($admin_email)
                ->setTo($email)
                ->setBody('Your confirmation link: ' . $link)

            ;
            $this->get('mailer')->send($message);

            $message = \Swift_Message::newInstance()
                ->setSubject('Your partner link')
                //TODO: send to config
                ->setFrom($admin_email)
                ->setTo($email)
                ->setBody('Your partner code: ' . $partner_code)

            ;
            $this->get('mailer')->send($message);
        }
    }

    public function processUserRegistration($object, $partner_code) {
        $userRepository = $this->getDoctrine()
            ->getRepository('RegBundle:User');

        $user = $userRepository->findOneByPartnerCode($partner_code);
        $object->setType(User::TYPE_USER);
        if (is_object($user)) {
            $balance = $user->getBalance();
            $balance += User::BONUS_FOR_USER;
            $user->setBalance($balance);
            $this->em->persist($user);
        }
        $this->em->persist($object);
        $this->em->flush();
    }

    public function activateAction(Request $request) {
        $this->em = $this->getDoctrine()->getManager();

        $userRepository = $this->getDoctrine()
            ->getRepository('RegBundle:User');
        $user = $userRepository->findOneByConfirmCode($request->get('code'));

        if (is_object($user)) {
            $user->setIsConfirmed(User::IS_CONFIRMED_TRUE);
            $this->em->persist($user);
            $this->em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Activated successfully!');
        } else {
            $this->get('session')->getFlashBag()->add('notice', 'Error!');
        }

        return new RedirectResponse($this->generateUrl('partners_reg_homepage'));
    }

    public function userAction(Request $request) {
        $this->em = $this->getDoctrine()->getManager();

        $user = new User();
        $request->attributes->set('redirect_to', $this->generateUrl('partners_reg_user'));
        $form = $this->createForm(new UserType(), $user);

        $result = $this->processForm($form, $request, true);
        if (is_object($result)) {
            $this->get('session')->getFlashBag()->add('notice', 'Successfully!');
            return $result;
        }

        return $this->render(
            'RegBundle:Default:index.html.twig',
            array(
                'form' => $form->createView(),
                'type_user' => User::TYPE_USER,
                'type_partner' => User::TYPE_PARTNER,
                'user_only' => true
            )
        );
    }
}
