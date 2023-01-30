<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ObjectRepository;

abstract class AbstractBaseManager
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
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
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}