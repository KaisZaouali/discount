<?php
declare(strict_types=1);

namespace App\Application\Actions\Discount;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Product\ProductAction;
require("helper.php");

class GetDiscountAction extends ProductAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $items= $this->getFormData()->items;

        /**
         * set product category for each order product
         */
        $items = setProductCategory($items, $this->productRepository );

        /**
         * apply the cheapest product discount
         */
        $items = discountCheapestProducts($items, "1", 2, 0.2);

        /**
         * apply the quantity discount
         */
        $items = discountByQuantity($items, "2", 5);

        /**
         * apply the total discount
         */
        $total = discountByTotal($items, 1000, 0.1);

        $discount= toFixed(floatval($this->getFormData()->total) - $total);

        return $this->respondWithData([
            "discount"=> $discount,
            "total" => toFixed($this->getFormData()->total),
            "total-with-discount" => $total
        ]);
    }
}
