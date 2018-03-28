<?php
/**
 * User: alex
 * Date: 3/27/18
 * Time: 8:12 AM
 */

namespace AppBundle\Twig;


use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AclExtension extends AbstractExtension
{
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * AclExtension constructor.
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('has_access', [$this, 'hasAccess'])
        ];
    }

    public function hasAccess($action, $object)
    {
        return $this->authorizationChecker->isGranted($action, $object);
    }

}