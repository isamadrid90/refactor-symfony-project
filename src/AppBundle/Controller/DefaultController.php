<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/create-user", name="user-form")
     */
    public function createUserAction(Request $request)
    {
        $message = "";
        $user = new User();

        /** @var Form $form */
        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('email', TextType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('save', SubmitType::class, array('label' => 'Create User'))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            if (!$entityManager->getRepository("AppBundle:User")->findBy(['email' => $user->getEmail()])) {
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();

                $message = "CONGRATS, the new user was created successfully";
                $user = new User();
                /** @var Form $form */
                $form = $this->createFormBuilder($user)
                    ->add('name', TextType::class)
                    ->add('surname', TextType::class)
                    ->add('email', TextType::class)
                    ->add('password', RepeatedType::class, array(
                        'type' => PasswordType::class,
                        'invalid_message' => 'The password fields must match.',
                        'options' => array('attr' => array('class' => 'password-field')),
                        'required' => true,
                        'first_options'  => array('label' => 'Password'),
                        'second_options' => array('label' => 'Repeat Password'),
                    ))
                    ->add('save', SubmitType::class, array('label' => 'Create User'))
                    ->getForm();
            } else {
                $message = "Sorry, a user with that email already exists";
            }
        }

        return $this->render('AppBundle:User:create-user.html.twig', array(
            'form' => $form->createView(),
            'message' => $message
        ));

    }
}
