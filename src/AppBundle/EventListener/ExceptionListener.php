<?php
/**
 * User: alex
 * Date: 3/27/18
 * Time: 10:44 AM
 */

namespace AppBundle\EventListener;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $response = new Response();
        $message = $exception->getMessage();

        if ($exception instanceof HttpExceptionInterface) {

            if ($exception->getStatusCode() === Response::HTTP_NOT_FOUND) {
                $message = 'Nothing to see here';
            }

            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->setContent($message);

        $event->setResponse($response);
    }
}