<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Adding the following annotation means that all routes defined in this
 * controller will be prefixed by "/demo/secured" (e.g. "/demo/secured/login")
 *
 * @Route("/demo/secured")
 */
class SecuredController extends Controller
{
    /**
     * @Route("/login", name="_demo_login")
     * @Template()
     */
    public function loginAction()
    {
        $request = $this->get('request');

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
            $request->getSession()->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_demo_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    /**
     * The URL to this controller is "/demo/secured/hello/{name}", which is
     * the route prefix ("/demo/secured") plus this route ("/hello/{name}").
     *
     * The URL "/demo/secured/hello/{name}" requires the ROLE_USER role to
     * be accessed, which is defined inside app/config/security.yml under
     * the "access_control" section.
     *
     * @Route("/hello", defaults={"name"="World"}),
     * @Route("/hello/{name}", name="_demo_secured_hello")
     * @Template()
     */
    public function helloAction($name)
    {
        return array('name' => $name);
    }

    /**
     * This controller requires the ROLE_ADMIN role, which is enforced using
     * the "@Secure" annotation below and the SecurityExtraBundle
     *
     * @Route("/hello/admin/{name}", name="_demo_secured_hello_admin")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function helloAdminAction($name)
    {
        return array('name' => $name);
    }
}
