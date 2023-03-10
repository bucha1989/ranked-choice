<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Form\DTO\EditProductModel;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Form\Form;

class ProductFormHandler
{
    private FileSaver $fileSaver;
    private ProductManager $productManager;

    public function __construct(ProductManager $productManager, FileSaver $fileSaver)
    {
        $this->fileSaver = $fileSaver;
        $this->productManager = $productManager;
    }

    /**
     * @param EditProductModel $editProductModel
     * @param Form $form
     * @return Product|null
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function productEditForm(EditProductModel $editProductModel, Form $form): Product
    {
        $product = new Product();

        if ($editProductModel->id) {
            $product = $this->productManager->find($editProductModel->id);
        }

        $product->setTitle($editProductModel->title);
        $product->setPrice($editProductModel->price);
        $product->setQuantity($editProductModel->quantity);
        $product->setDescription($editProductModel->description);
        $product->setIsPublished($editProductModel->isPublished);
        $product->setIsDeleted($editProductModel->isDeleted);
        $product->setCategory($editProductModel->category);

        $this->productManager->save($product);

        $newImageFile = $form->get('newImage')->getData();
        $tempImageFilename = $newImageFile
            ? $this->fileSaver->saveUploadFileToTemp($newImageFile)
            : null;
        $this->productManager->updateProductImages($product, $tempImageFilename);
        $this->productManager->save($product);

        return $product;
    }

}