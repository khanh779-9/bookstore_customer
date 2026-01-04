<?php

class ProductController extends BaseController
{
    public function index()
    {
        $products = ProductModel::getAllProducts();
        $this->render('products', ['products' => $products]);
    }

    public function show()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = ProductModel::getProductById($id);
        if (empty($product)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Product not found';
            return;
        }
        $this->render('productview', ['product' => $product]);
    }
}
