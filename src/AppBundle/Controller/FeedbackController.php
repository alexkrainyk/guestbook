<?php
/**
 * User: alex
 * Date: 3/27/18
 * Time: 10:30 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Feedback;
use AppBundle\Form\FeedbackType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/feedback")
 */
class FeedbackController extends Controller
{
    /**
     * @Route("/list", name="feedback_list")
     */
    public function indexFeedbackAction()
    {
        $feedback = new Feedback();

        $form = $this->createFeedbackForm($feedback);
        $em = $this->getDoctrine()->getManager();
        $feedbacks = $em->getRepository('AppBundle:Feedback')->findBy([], ['created' => 'DESC']);

        return $this->render('feedback/list.html.twig', [
            'form' => $form->createView(),
            'feedbacks' => $feedbacks
        ]);
    }


    /**
     * @Route("/add", name="add_feedback")
     * @Method("POST")
     */
    public function addFeedbackAction(Request $request)
    {
        $feedback = new Feedback();

        $form = $this->createFeedbackForm($feedback);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $tokenStorage = $this->get('security.token_storage');
            $user = $tokenStorage->getToken()->getUser();
            $feedback->setAuthor($user->getName());
            $em->persist($feedback);
            $em->flush();

            // creating the ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($feedback);
            $acl = $aclProvider->createAcl($objectIdentity);
            $securityIdentity = UserSecurityIdentity::fromAccount($user);
            $securityManagerIdentity = new RoleSecurityIdentity('ROLE_MANAGER');

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $acl->insertClassAce($securityManagerIdentity, MaskBuilder::MASK_MASTER);
            $aclProvider->updateAcl($acl);

            return $this->redirectToRoute('feedback_list');
        }

        $feedbacks = $em->getRepository('AppBundle:Feedback')->findBy([], ['created' => 'DESC']);

        return $this->render('feedback/list.html.twig', [
            'form' => $form->createView(),
            'feedbacks' => $feedbacks
        ]);
    }


    /**
     * @Route("/{id}/edit", name="edit_feedback")
     * @Method("GET")
     */
    public function editFeedbackAction(Feedback $feedback)
    {
        $authorizationChecker = $this->get('security.authorization_checker');

        // check for edit access
        if (false === $authorizationChecker->isGranted('EDIT', $feedback)) {
            throw new AccessDeniedException();
        }

        $form = $this->createEditFeedbackForm($feedback);

        return $this->render('feedback/edit.html.twig', [
            'form' => $form->createView(),
            'feedback' => $feedback
        ]);
    }

    /**
     * @Route("/{id}/edit", name="update_feedback")
     * @Method("POST")
     */
    public function updateFeedbackAction(Request $request, Feedback $feedback)
    {
        $authorizationChecker = $this->get('security.authorization_checker');

        if (false === $authorizationChecker->isGranted('EDIT', $feedback)) {
            throw new AccessDeniedException();
        }

        $form = $this->createEditFeedbackForm($feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            return $this->redirectToRoute('feedback_list');
        }

        return $this->render('feedback/edit.html.twig', [
            'form' => $form->createView(),
            'feedback' => $feedback
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete_feedback")
     * @Method("GET")
     */
    public function deleteFeedbackAction(Feedback $feedback)
    {
        $authorizationChecker = $this->get('security.authorization_checker');

        if (false === $authorizationChecker->isGranted('DELETE', $feedback)) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($feedback);
        $em->flush();

        return $this->redirectToRoute('feedback_list');
    }

    /**
     * @param Feedback $feedback
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createFeedbackForm(Feedback $feedback)
    {
        return $this->createForm(FeedbackType::class, $feedback, [
            'method' => 'POST',
            'action' => $this->generateUrl('add_feedback')
        ]);
    }

    /**
     * @param Feedback $feedback
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createEditFeedbackForm(Feedback $feedback)
    {
        return $this->createForm(FeedbackType::class, $feedback, [
            'method' => 'POST',
            'action' => $this->generateUrl('update_feedback', ['id' => $feedback->getId()])
        ]);
    }
}