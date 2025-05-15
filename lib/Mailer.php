<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Configurações do servidor SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'soterocaue2@gmail.com';
        // IMPORTANTE: Use uma senha de aplicativo gerada no Google Account
        // Para gerar: https://myaccount.google.com/apppasswords
        $this->mailer->Password = 'ojpd kzml uhwa llie'; // Substitua pela senha de aplicativo gerada
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->CharSet = 'UTF-8';
        
        // Configurações padrão
        $this->mailer->isHTML(true);
        $this->mailer->setFrom('soterocaue2@gmail.com', 'Mini ERP');
        
        // Habilita debug em caso de erro
        $this->mailer->SMTPDebug = 3; // 3 = mensagens cliente/servidor + mensagens de baixo nível
        $this->mailer->Debugoutput = function($str, $level) {
            $logFile = __DIR__ . '/../logs/mail.log';
            $logDir = dirname($logFile);
            
            // Cria o diretório de logs se não existir
            if (!file_exists($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            // Adiciona timestamp ao log
            $logMessage = date('Y-m-d H:i:s') . " - Level $level: $str\n";
            
            // Escreve no arquivo de log
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            // Também registra no error_log do PHP
            error_log("PHPMailer Debug: $str");
        };
    }
    
    public function sendOrderConfirmation($order, $items) {
        try {
            // Log inicial
            error_log("Iniciando envio de email para pedido #" . $order['id']);
            error_log("Dados do pedido: " . print_r($order, true));
            error_log("Itens do pedido: " . print_r($items, true));
            
            $this->mailer->clearAddresses(); // Limpa endereços anteriores
            $this->mailer->addAddress($order['customer_email'], $order['customer_name']);
            $this->mailer->Subject = 'Confirmação de Pedido #' . $order['id'];
            
            // Corpo do email em HTML
            $body = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .order-info { margin-bottom: 20px; }
                    .items { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    .items th, .items td { padding: 10px; border: 1px solid #ddd; }
                    .items th { background: #f5f5f5; }
                    .total { text-align: right; font-weight: bold; }
                    .footer { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>Pedido Confirmado!</h1>
                    </div>
                    <div class="content">
                        <p>Olá ' . htmlspecialchars($order['customer_name']) . ',</p>
                        <p>Seu pedido foi recebido e está sendo processado.</p>
                        
                        <div class="order-info">
                            <h2>Informações do Pedido</h2>
                            <p><strong>Número do Pedido:</strong> #' . $order['id'] . '</p>
                            <p><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($order['created_at'])) . '</p>
                            <p><strong>Status:</strong> ' . ucfirst($order['status']) . '</p>
                        </div>
                        
                        <h2>Itens do Pedido</h2>
                        <table class="items">
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço</th>
                                <th>Subtotal</th>
                            </tr>';
            
            foreach ($items as $item) {
                $body .= '
                <tr>
                    <td>' . htmlspecialchars($item['name']) . '</td>
                    <td>' . $item['quantity'] . '</td>
                    <td>R$ ' . number_format($item['price'], 2, ',', '.') . '</td>
                    <td>R$ ' . number_format($item['subtotal'], 2, ',', '.') . '</td>
                </tr>';
            }
            
            $body .= '
                        </table>
                        
                        <div class="total">
                            <p><strong>Subtotal:</strong> R$ ' . number_format($order['subtotal'], 2, ',', '.') . '</p>
                            <p><strong>Frete:</strong> R$ ' . number_format($order['shipping'], 2, ',', '.') . '</p>';
            
            if ($order['discount'] > 0) {
                $body .= '<p><strong>Desconto:</strong> -R$ ' . number_format($order['discount'], 2, ',', '.') . '</p>';
            }
            
            $body .= '
                            <p><strong>Total:</strong> R$ ' . number_format($order['total'], 2, ',', '.') . '</p>
                        </div>
                        
                        <div class="delivery-info">
                            <h2>Informações de Entrega</h2>
                            <p><strong>Endereço:</strong><br>
                            ' . htmlspecialchars($order['customer_address']) . '<br>
                            CEP: ' . htmlspecialchars($order['customer_zipcode']) . '</p>
                        </div>
                        
                        <div class="footer">
                            <p>Obrigado por comprar conosco!</p>
                            <p>Em caso de dúvidas, entre em contato conosco.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>';
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace('<br>', "\n", $body));
            
            error_log("Tentando enviar email...");
            $result = $this->mailer->send();
            
            if (!$result) {
                error_log("Erro ao enviar email: " . $this->mailer->ErrorInfo);
                return false;
            }
            
            error_log("Email enviado com sucesso!");
            return true;
        } catch (Exception $e) {
            error_log("Exceção ao enviar email: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
} 
