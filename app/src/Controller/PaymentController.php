<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentAddType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PaymentController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $payment = new Payment();
        $link = $this->generateUrl('show_fake_merchant', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $payment->setMerchantLink($link);

        $form = $this->createForm(PaymentAddType::class, $payment, [
            'action' => $this->generateUrl('index'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($payment);
            $this->em->flush();

            return $this->redirectToRoute('send_payment', ['id' => $payment->getId()]);
        }

        return $this->render('payment/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param ProducerInterface $producer
     * @param Request $request
     * @param $id
     * @return Response
     * @throws EntityNotFoundException
     */
    public function sendPaymentAction(ProducerInterface $producer, Request $request, $id)
    {
        $payment = $this->em->getRepository(Payment::class)->findOneBy(['id'=> $id]);

        if (!$payment) {
            throw new EntityNotFoundException('Payment does not exist');
        }

        $producer->publish($id);

        return $this->render('payment/wait_payment.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showFakeMerchantAction(Request $request)
    {
        $status = null;
        if ($request->query->has('status')) {
            $status = $request->query->get('status');
        }

        return $this->render('payment/merchant.html.twig', [
            'status' => $status
        ]);
    }
}
