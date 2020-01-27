<?php


namespace App\Rabbit;

use App\Entity\Payment;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;


class PaymentConsumer implements ConsumerInterface
{

    private $delayedProducer;

    private $entityManager;

    /**
     * PaymentConsumer constructor.
     * @param ProducerInterface $delayedProducer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ProducerInterface $delayedProducer, EntityManagerInterface $entityManager)
    {
        $this->delayedProducer = $delayedProducer;
        $this->entityManager = $entityManager;

        gc_enable();
    }

    /**
     * @param AMQPMessage $msg
     * @return mixed|void
     * @throws EntityNotFoundException
     */
    public function execute(AMQPMessage $msg)
    {
        $payment =  $this->entityManager->getRepository(Payment::class)->findOneBy(['id'=>$msg->getBody()]);

        if (!$payment) {
            throw new EntityNotFoundException('Payment with id'. $msg->getBody() .' not exists');
        }

        try {
            if ($payment->getCardNumber() == Payment::BAD_CARD_NUMBER) {
                throw new \Exception();
            }
            $payment->setStatus(Payment::PAYMENT_SUCCESS);

        } catch (\Exception $exception) {
            $payment->setStatus(Payment::PAYMENT_ERROR);
        }

        $this->entityManager->flush();
    }
}