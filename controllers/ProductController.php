<?php
require_once './models/Product.php';
require_once './models/Stock.php';
require_once './config/database.php';

class ProductController {
    private $db;
    private $productModel;
    private $stockModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->productModel = new Product($this->db);
        $this->stockModel = new Stock($this->db);
    }

    public function index() {
        $products = $this->productModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        include './views/products/list.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'price' => $_POST['price'],
                'description' => $_POST['description'],
                'variations' => json_encode($_POST['variations'])
            ];

            if ($this->productModel->create($data)) {
                $productId = $this->productModel->getLastInsertId();
                
                $stockData = [
                    'product_id' => $productId,
                    'quantity' => $_POST['stock']
                ];
                $this->stockModel->create($stockData);
                
                $_SESSION['success'] = 'Produto cadastrado com sucesso!';
                header('Location: ?page=products');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar produto';
            }
        }
        include './views/products/create.php';
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $id,
                'name' => $_POST['name'],
                'price' => $_POST['price'],
                'description' => $_POST['description'],
                'variations' => json_encode($_POST['variations'])
            ];

            if ($this->productModel->update($data)) {
                $stockData = [
                    'product_id' => $id,
                    'quantity' => $_POST['stock']
                ];
                $this->stockModel->update($stockData);
                
                $_SESSION['success'] = 'Produto atualizado com sucesso!';
                header('Location: ?page=products');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar produto';
            }
        }
        
        $product = $this->productModel->readById($id);
        $stock = $this->stockModel->getByProductId($id);
        include './views/products/edit.php';
    }

    public function delete($id) {
        if ($this->productModel->delete($id)) {
            $_SESSION['success'] = 'Produto excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir produto';
        }
        header('Location: ?page=products');
        exit;
    }
}
?>