<?php

namespace App\Utils\Manager;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ObjectRepository;

class OrderManager extends AbstractBaseManager
{
    /**
     * @ORM\Column(type="string")
     */
    private CartManager $cartManager;

    public function __construct(EntityManagerInterface $entityManager,
                                CartManager            $cartManager)
    {
        parent::__construct($entityManager);
        $this->cartManager = $cartManager;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Order::class);
    }


    /**
     * @param string $sessionId
     * @return void
     */
    public function createOrderFromCartBySessionId(string $sessionId, User $user)
    {
        $cart = $this->cartManager->getRepository()->findOneBy(['sessionId' => $sessionId]);

        if ($cart) {
            $this->createOrderFromCart($cart, $user);
        }
    }

    public function createOrderFromCart(Cart $cart, User $user)
    {
        $order = new Order();
        $order->setOwner($user);
        $order->setStatus(OrderStaticStorage::ORDER_STATUS_CREATED);
        $orderTotalPrice = 0;

        /** @var Cart $cartProduct */
        foreach ($cart->getCartProducts()->getValues() as $cartProduct) {
            $product = $cartProduct->getProduct();

            $orderProduct = new OrderProduct();
            $orderProduct->setAppOrder($order);
            $orderProduct->setQuantity($cartProduct->getQuantity());
            $orderProduct->setPricePerOne($product->getPrice());
            $orderProduct->setProduct($product);

            $orderTotalPrice += $orderProduct->getQuantity() * $orderProduct->getPricePerOne();

            $order->addOrderProduct($orderProduct);
            $this->entityManager->persist($orderProduct);

        }

        $order->setTotalPrice($orderTotalPrice);
        $order->getUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->cartManager->remove($cart);

        dd($order);
    }

    /**
     * @param Order $order
     * @return void
     */
    public function remove(object $order)
    {

        $order->setIsDeleted(true);

//        /** @var Product $product */
//        foreach ($category->getProducts()->getValues() as $product) {
//            $product->setIsDeleted(true);
//        }

        $this->save($order);
    }
}