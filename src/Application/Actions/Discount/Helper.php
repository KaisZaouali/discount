<?php

use App\Domain\Product\ProductRepository;

/**
 * set the category for each item
 * 
 * @param array                $items
 * @param ProductRepository    $productRepository
 * @return array
 */
function setProductCategory($items, $productRepository)
{
    $products =[];
    foreach ($items as $item) {
        $product = $productRepository->findProductOfId($item->{'product-id'});
        if($product)
            {
                $productWithCategory = [
                    "productId" => $item->{'product-id'},
                    "quantity" => intval($item->quantity),
                    "unitPrice" => floatval($item->{'unit-price'}),
                    "total" => floatval($item->total),
                    "category" => $product->getCategory(),
                ];
                array_push($products, $productWithCategory);
            }
    }
    return $products;
}

/**
 * make the discount for the cheapest product of the category if its quantity is more
 * than the min quantity
 * 
 * @param array     $items
 * @param string    $category
 * @param int       $minQuantity
 * @param float     $discountMultiplier
 * @return array
 */
function discountCheapestProducts($items, $category, $minQuantity, $discountMultiplier)
{
	$itemsOfCategory = array_filter($items, function($item) use ($category)
	{
		return $item['category'] == $category;
	});

	if(count($itemsOfCategory) < $minQuantity) return $items;

	function getCheapestProduct($carry, $item)
	{
		if($carry['unitPrice'] > $item['unitPrice']){
			$carry['unitPrice'] = $item['unitPrice'];
			$carry['productId'] = $item['productId'];
		}
		return $carry;
	}
	$cheapestProduct = array_reduce($itemsOfCategory, 'getCheapestProduct', ['unitPrice' => INF]);

	$items = array_map(function($item) use ($cheapestProduct, $discountMultiplier)
	{
		if($item['productId'] === $cheapestProduct['productId']){
			$item['total'] = toFixed((1 - $discountMultiplier) * $item['total']);
			return $item;
		}
		return $item;
	},
    $items);

	return $items;
}

/**
 * make the discount for the products of the category by quantity
 * 
 * @param array     $items
 * @param string    $category
 * @param int       $quantityThreshold
 * @return array
 */
function discountByQuantity ($items, $category, $quantityThreshold){
    $items = array_map(function($item) use ($category, $quantityThreshold)
	{
		if($item['category'] === $category && $item['quantity'] > $quantityThreshold){
            $discountQuantity = intdiv($item["quantity"], ($quantityThreshold + 1));
			$item['total'] = toFixed($item['total'] - ($discountQuantity * $item['unitPrice']));
			return $item;
		}
		return $item;
	},
    $items);
    return $items;
}

/**
 * make the discount for the products regarding the total price
 * 
 * @param array     $items
 * @param int       $totalThreshold
 * @param float     $discount
 * @return float
 */
function discountByTotal ($items, $totalThreshold, $discount){
    function getTotal($carry, $item)
	{
		$carry += $item['total'];
		return $carry;
	}
	$itemsTotal = array_reduce($items, 'getTotal', 0);
    
    if($itemsTotal > $totalThreshold) $itemsTotal = toFixed((1 - $discount) * $itemsTotal);
    return $itemsTotal;
}

/**
 * fix the float to two decimals
 * 
 * @param float     $number
 * @return float
 */
function toFixed ($number){
    return floatval(number_format($number, 2, '.', ''));
}




