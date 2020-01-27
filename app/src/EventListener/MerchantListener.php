<?php

namespace App\EventListener;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class MerchantListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $attributes = $request->attributes->all();

        if ($attributes['_route'] === 'send_payment') {

            $payment = $this->entityManager->getRepository(Payment::class)->findOneBy(['id'=>$attributes['id']]);

            if ($payment->getStatus() != Payment::PAYMENT_SENT) {
                $url = $payment->getMerchantLink() . '?status='.$payment->getStatus();
                $response = new RedirectResponse($url);
                $event->setResponse($response);
            }
        }
    }
}