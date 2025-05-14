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

Para configurar o envio de emails no sistema, siga os passos abaixo:

### 1. Configuração da Conta Gmail

1. Acesse sua conta Gmail
2. Ative a verificação em duas etapas:
   - Vá em "Gerenciar sua Conta Google"
   - Clique em "Segurança"
   - Procure por "Verificação em duas etapas" e ative

3. Gere uma senha de aplicativo:
   - Ainda em "Segurança"
   - Procure por "Senhas de app"
   - Selecione "Outro (Nome personalizado)"
   - Digite um nome (ex: "Mini ERP")
   - Clique em "Gerar"
   - Copie a senha gerada (16 caracteres)

### 2. Configuração no Sistema

1. Abra o arquivo `lib/Mailer.php`
2. Localize as configurações SMTP e atualize:
   ```php
   $mail->Username = 'seu-email@gmail.com'; // Seu email Gmail
   $mail->Password = 'sua-senha-de-app';    // Senha de 16 caracteres gerada
   ```

3. Atualize o remetente padrão:
   ```php
   $mail->setFrom('seu-email@gmail.com', 'Nome da Sua Loja');
   ```

### 3. Verificação das Extensões PHP

Certifique-se que as seguintes extensões estão habilitadas no php.ini:

```ini
extension=openssl
extension=php_openssl
extension=php_smtp
```

### 4. Teste do Envio

1. Faça um pedido de teste no sistema
2. Verifique a pasta `logs/mail.log` para ver os detalhes do envio
3. Se houver erros, verifique:
   - Se a senha de app está correta
   - Se as extensões PHP estão habilitadas
   - Se o firewall não está bloqueando a conexão SMTP

### 5. Solução de Problemas

Se os emails não estiverem sendo enviados:

1. Verifique o arquivo de log em `logs/mail.log`
2. Confirme se o debug está ativado no Mailer.php:
   ```php
   $mail->SMTPDebug = 3; // Nível de debug (0-4)
   ```
3. Verifique se a porta 587 está liberada no firewall
4. Confirme se o servidor tem permissão para conexões SMTP externas

### 6. Segurança

- Nunca compartilhe sua senha de app
- Mantenha o arquivo Mailer.php com permissões restritas
- Considere usar variáveis de ambiente para as credenciais
- Faça backup regular dos logs de email

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

Para suporte, envie um email para devcauesotero@gmail.com ou abra uma issue no GitHub.

## 🙏 Agradecimentos

- Bootstrap
- PHPMailer
- ViaCEP
- Todos os contribuidores
