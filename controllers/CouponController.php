<?php
require_once './models/Coupon.php';
require_once './config/database.php';

class CouponController {
    private $db;
    private $couponModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->couponModel = new Coupon($this->db);
    }

    public function index() {
        $coupons = $this->couponModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        include './views/coupons/list.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'discount_type' => $_POST['discount_type'],
                'discount_value' => $_POST['discount_value'],
                'min_order_value' => $_POST['min_order_value'] ?: null,
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'max_uses' => $_POST['max_uses'] ?: null
            ];

            if ($this->couponModel->create($data)) {
                $_SESSION['success'] = 'Cupom criado com sucesso!';
                header('Location: ?page=coupons');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao criar cupom';
            }
        }
        include './views/coupons/create.php';
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $id,
                'code' => $_POST['code'],
                'discount_type' => $_POST['discount_type'],
                'discount_value' => $_POST['discount_value'],
                'min_order_value' => $_POST['min_order_value'] ?: null,
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'max_uses' => $_POST['max_uses'] ?: null
            ];

            if ($this->couponModel->update($data)) {
                $_SESSION['success'] = 'Cupom atualizado com sucesso!';
                header('Location: ?page=coupons');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar cupom';
            }
        }
        
        $coupon = $this->couponModel->readById($id);
        include './views/coupons/edit.php';
    }

    public function delete($id) {
        if ($this->couponModel->delete($id)) {
            $_SESSION['success'] = 'Cupom excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir cupom';
        }
        header('Location: ?page=coupons');
        exit;
    }
}
?>