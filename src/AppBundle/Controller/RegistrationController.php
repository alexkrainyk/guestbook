<?php
/**
 * User: alex
 * Date: 3/27/18
 * Time: 6:33 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $user->setRegisteredAt(new \DateTime());
            $user->setRoles(['ROLE_CLIENT']);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('feedback_list');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}