<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExceptionListener
{
    /**
     * @var NormalizerInterface
     */
    private NormalizerInterface $normalizer;

    /**
     * @param NormalizerInterface $normalizer
     */
    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return void
     * @throws ExceptionInterface
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        if (str_starts_with($request->attributes->get('_route'), 'api_v1_')) {
            $response = $this->createApiResponse($exception);
            $event->setResponse($response);
        }
    }

    /**
     * @param \Exception $exception
     *
     * @return Response
     * @throws ExceptionInterface
     */
    private function createApiResponse(\Exception $exception): Response
    {
        $normalized = $this->normalizer->normalize($exception, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['trace', 'traceAsString', 'file', 'line', 'previous'],
        ]);

        return new JsonResponse([
            'error' => $normalized,
        ], 400);
    }
}
