<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;


class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof NotFoundHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Ressource : non trouvée ! :=( '
            ];

            $response = new JsonResponse($data);
            $event->setResponse($response);
        }

        if($exception instanceof NotEncodableValueException) {
			$data = [
				'message' => 'Une erreur serveur est survenue : veuillez vérifier le format de votre Json !'
			];

			$response = new JsonResponse($data);
			$event->setResponse($response);
		}

        $exception = $event->getThrowable();
        if ($exception instanceof  AccessDeniedHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => "Ressource : Vous n'êtes pas autorisé à exécuter cet action ! "
            ];

            $response = new JsonResponse($data);
            $event->setResponse($response);
        }

    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
