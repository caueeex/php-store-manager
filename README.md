# Mini ERP PHP

Um sistema de gerenciamento empresarial simples e eficiente, desenvolvido em PHP, que permite gerenciar produtos, estoque, pedidos e cupons de desconto.

## 🚀 Funcionalidades

- **Gestão de Produtos**
  - Cadastro de produtos com nome, preço e descrição
  - Controle de estoque
  - Variações de produtos (tamanhos, cores, etc.)

- **Carrinho de Compras**
  - Adição/remoção de produtos
  - Atualização de quantidades
  - Cálculo automático de subtotal
  - Aplicação de cupons de desconto

- **Cálculo de Frete**
  - Integração com ViaCEP
  - Cálculo automático baseado no valor do pedido
  - Frete grátis para compras acima de R$ 200,00

- **Pedidos**
  - Processamento de pedidos
  - Confirmação por email
  - Atualização automática do estoque
  - Histórico de pedidos

- **Cupons de Desconto**
  - Criação de cupons
  - Descontos em porcentagem ou valor fixo
  - Validação de cupons

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Composer (para gerenciamento de dependências)
- Extensões PHP:
  - PDO
  - PDO_MySQL
  - OpenSSL
  - cURL
  - mbstring

## 🔧 Instalação

1. Clone o repositório:
```bash
git clone https://github.com/caueeex/php-store-manager.git
cd mini-erp-php
```

2. Instale as dependências via Composer:
```bash
composer install
```

3. Configure o banco de dados:
   - Crie um banco de dados MySQL
   - Importe o arquivo `database.sql` que está na raiz do projeto
   - Configure as credenciais do banco em `config/database.php`

4. Configure o servidor web:
   - Para Apache, certifique-se que o mod_rewrite está habilitado
   - Configure o DocumentRoot para a pasta do projeto
   - Certifique-se que a pasta tem permissões de escrita

5. Configure o envio de emails:
   - Edite o arquivo `lib/Mailer.php`
   - Configure as credenciais SMTP do Gmail
   - Gere uma senha de aplicativo no Google Account

## ⚙️ Configuração

1. **Banco de Dados**
   - Edite o arquivo `config/database.php`
   - Configure host, nome do banco, usuário e senha

2. **Email**
   - Edite o arquivo `lib/Mailer.php`
   - Configure o email e senha do Gmail
   - Gere uma senha de aplicativo em: https://myaccount.google.com/apppasswords

3. **Constantes do Sistema**
   - Edite o arquivo `config/constants.php`
   - Configure o nome do site, email de contato, etc.

## 🚀 Uso

1. Acesse o sistema pelo navegador
   
2. **Gerenciamento de Produtos**
   - Adicione produtos com nome, preço e estoque
   - Configure variações se necessário
   - Gerencie o estoque

3. **Cupons de Desconto**
   - Crie cupons com código, tipo e valor
   - Defina data de validade
   - Aplique em pedidos

4. **Pedidos**
   - Visualize pedidos recebidos
   - Atualize status
   - Envie confirmações por email

## 📧 Configuração de Email

O sistema usa o Gmail SMTP para envio de emails. Para configurar:

1. Acesse sua conta Google
2. Ative a verificação em duas etapas
3. Gere uma senha de aplicativo
4. Configure no arquivo `lib/Mailer.php`

## 🔒 Segurança

- Proteção contra SQL Injection
- Validação de dados
- Sanitização de inputs
- Controle de sessão

## 🛠️ Estrutura do Projeto

```
mini-erp-php/
├── config/
│   ├── database.php
│   ├── constants.php
│   └── functions.php
├── controllers/
│   ├── CartController.php
│   ├── ProductController.php
│   └── OrderController.php
├── models/
│   ├── Product.php
│   ├── Cart.php
│   └── Order.php
├── views/
│   ├── products/
│   ├── cart/
│   └── orders/
├── lib/
│   └── Mailer.php
├── logs/
├── vendor/
└── index.php
```

## 📝 Logs

- Os logs do sistema são armazenados em `logs/`
- Logs de email em `logs/mail.log`
- Logs de erro do PHP em `logs/php_error.log`

## 🤝 Contribuindo

1. Faça um Fork do projeto
2. Crie uma Branch para sua Feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a Branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## ✨ Recursos Adicionais

- Interface responsiva com Bootstrap 5
- Ícones com Bootstrap Icons
- Validação de formulários
- Máscaras de input
- Cálculo automático de valores
- Integração com ViaCEP

## 🆘 Suporte

Para suporte, envie um email para soterocaue2@gmail.com ou abra uma issue no GitHub.

## 🙏 Agradecimentos

- Bootstrap
- PHPMailer
- ViaCEP
- Todos os contribuidores
