<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ObjectRepository;

class ProductManager extends AbstractBaseManager
{
    private string $productImagesDir;
    private ProductImagesManager $imagesManager;

    public function __construct(EntityManagerInterface $entityManager,
                                string                 $productImagesDir,
                                ProductImagesManager   $imagesManager)
    {
        parent::__construct($entityManager);
        $this->productImagesDir = $productImagesDir;
        $this->imagesManager = $imagesManager;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    /**
     * @param object $product
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(object $product)
    {
        $product->setIsDeleted(true);
        $this->save($product);
    }

    public function getProductImagesDir(Product $product): string
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }

    /**
     * @param Product $product
     * @param string|null $tempImageFilename
     * @return Product
     */
    public function updateProductImages(Product $product, string $tempImageFilename = null): Product
    {
        if (!$tempImageFilename) {
            return $product;
        }

        $productDir = $this->getProductImagesDir($product);
        $productImage = $this->imagesManager->saveImageForProduct($productDir, $tempImageFilename);
        $productImage->setProduct($product);
        $product->addProductImage($productImage);

        return $product;

    }
}