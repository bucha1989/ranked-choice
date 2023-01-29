<?php

namespace App\Form\DTO;

use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

class EditCategoryModel
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank(message="Please enter a valid title.")
     */
    public $title;

    /**
     * @param Category|null $category
     * @return static
     */
    public static function makeFromCategory(?Category $category): self
    {
        $model = new self();

        if (!$category) {
            return $model;
        }

        $model->id = $category->getId();
        $model->title = $category->getTitle();

        return $model;
    }
}