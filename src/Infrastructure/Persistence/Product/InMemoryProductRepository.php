<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductNotFoundException;
use App\Domain\Product\ProductRepository;

class InMemoryProductRepository implements ProductRepository
{
    /**
     * @var Product[]
     */
    private $products;

    /**
     * InMemoryProductRepository constructor.
     *
     * @param array|null $products
     */
    public function __construct(array $products = null)
    {
        $this->products = $products ?? [
            "A101" => new Product("A101", "Screwdriver", "1", "9.75"),
            "A102" => new Product("A102", "Electric screwdriver", "1", "49.50"),
            "B101" => new Product("B101", "Basic on-off switch", "2", "4.99"),
            "B102" => new Product("B102", "Press button", "2", "4.99"),
            "B103" => new Product("B103", "Switch with motion detector", "2", "12.95"),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->products);
    }

    /**
     * {@inheritdoc}
     */
    public function findProductOfId(string $id): Product
    {
        if (!isset($this->products[$id])) {
            throw new ProductNotFoundException();
        }

        return $this->products[$id];
    }
}
