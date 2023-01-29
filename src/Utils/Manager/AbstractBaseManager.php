<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ObjectRepository;

abstract class AbstractBaseManager
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @return ObjectRepository
     */
    abstract public function getRepository(): ObjectRepository;

    public function find(string $id): ?object
    {
        return $this->getRepository()->find($id);
    }
    /**
     * @param object $product
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(object $entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

    }

    /**
     * @param object $entity
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(object $entity)
    {
        $entity->setIsDeleted(true);
        $this->save($entity);
    }
}