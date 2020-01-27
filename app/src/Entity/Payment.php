<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    const PAYMENT_SENT = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_ERROR = 2;


    const BAD_CARD_NUMBER = '4123231111111111';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="card_number", type="string", nullable=false)
     * @Assert\CardScheme( schemes={"VISA"}, message="Your credit card number is invalid.")
     */
    private $cardNumber;

    /**
     * @ORM\Column(name="cvv", type="integer", nullable=false)
     * @Assert\Length(max="3", min="3")
     */
    private $cvv;

    /**
     * @ORM\Column(name="holder", type="string", length=255, nullable=false)
     * @Assert\Length(max="255")
     */
    private $holder;

    /**
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::PAYMENT_SENT;

    /**
     * @ORM\Column(name="merchant_link", type="string")
     */
    private $merchantLink;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * @param mixed $cvv
     */
    public function setCvv($cvv): void
    {
        $this->cvv = $cvv;
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param mixed $cardNumber
     */
    public function setCardNumber($cardNumber): void
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return mixed
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @param mixed $holder
     */
    public function setHolder($holder): void
    {
        $this->holder = $holder;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getMerchantLink()
    {
        return $this->merchantLink;
    }

    /**
     * @param mixed $merchantLink
     */
    public function setMerchantLink($merchantLink): void
    {
        $this->merchantLink = $merchantLink;
    }

}
