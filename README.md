# paypal-integracao-php
### Aplicação PHP de integração com a API do Paypal para recebimento de pagamentos

Esta aplicação foi desenvolvida utilizando o PHP como backend, banco de dados MySQL e para o frontend foram utilizados HTML, CSS e Javascript.

Esta aplicação contém scripts para utilização das seguintes APIs no momento: Paypal IPN e Paypal Orders. Consiste em um sistema de compra de créditos para demonstração do funcionamento da integração.
Para qualquer dúvida ou contratar para novos trabalhos mandar email para rrodrigues.dev01@gmail.com.

### Paypal IPN requisitos
Para testar a API do Paypal IPN é necessário ter o business email correspondente da sua conta sandbox ou de produção e o certificado no formato .pem fornecido pelo Paypal.

![paypal_ipn_sandbox_accounts](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/paypal_ipn_sandbox_accounts.png)

### Paypal Orders requisitos
Para testar a API do Paypal Orders é necessário ter uma aplicação Rest API correspondente da sua conta sandbox ou de produção do Paypal.

![paypal_rest_api_aplicacao](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/paypal_rest_api_aplicacao.png)

### Ambiente utilizado
PHP versão 8.3.26

### Passos para utilização
#### 1. Baixe o repositório
<pre>git clone https://github.com/rodriguesrenato61/paypal-integracao-php.git</pre>

#### 2. Abra o terminal na pasta do repositório e digite o comando para instalar as dependências necessárias. Necessário ter o composer instalado.
<pre>composer dump-autoload -o</pre>

#### 3. Crie o banco de dados MySQL com os scripts no arquivo banco.sql, não é obrigatório a criação das chaves estrangeiras situadas nos scripts, neste caso é opcional.

#### 4. Coloque as suas credenciais de autenticação CLIENT_ID e CLIENT_SECRET da API do Paypal no arquivo config.php e também a url raíz da sua aplicação em BASE_URL, juntamente com as outras variáveis de ambiente de acordo com as descrições.
<pre>
const BASE_URL = "http://localhost/paypal";
const DB_HOST = "localhost";
const DB_NAME = "paypal_db";
const DB_USER = "root";
const DB_PASSWORD = "";
const LOGS_WEBHOOK_DIR = "C://laragon/www/paypal/resources/logs";//diretório de logs do webhook deve ser um diretório inacessível pelo navegador
const REQUESTS_DIR = "C://laragon/www/paypal/testes/requests";//diretório de notificações POST enviadas para o webhook do Paypal IPN, utilizado para testar este webhook, deve ser um diretório inacessível pelo navegador

const PAYPAL_TIPO = "order";//ipn ou order (tipo de integração a ser testada)
const PAYPAL_EMAIL_BUSINESS = "";
const PAYPAL_CLIENT_ID_SANDBOX = "";
const PAYPAL_CLIENT_ID_PRODUCTION = "";
const PAYPAL_CLIENT_SECRET_SANDBOX = "";
const PAYPAL_CLIENT_SECRET_PRODUCTION = "";
const PAYPAL_AMBIENTE = "sandbox";//sandbox ou production
const PAYPAL_CURRENCY_CODE = "BRL";
const PAYPAL_CERTIFICADO = "C://laragon/www/paypal/resources/certificado/paypal_cacert.pem";//deve ser um diretório inacessível pelo navegador
</pre>

#### 5. Inicie o servidor da aplicação e abra o navegador com a url da página do usuário de acordo com a BASE_URL configurada. Esta página mostra a quantidade de créditos e outras informações do usuário, exemplo: http://localhost/paypal/pages/usuario.php

![usuario](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/usuario.png)

#### 6. Clique no link + CREDIT para acessar a página de compra de créditos, dependendo da variável de ambiente PAYPAL_TIPO a página de compra utilizará a API do Paypal IPN ou Paypal Orders.

![comprar](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/comprar.png)

![paypal_checkout_etapa_1](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/paypal_checkout_etapa_1.png)

#### 7. Após o processamento e aprovação do pagamento você será redirecionado para página de informações do pagamento realizado.

![pagamento](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/pagamento.png)

#### 8. Ao clicar em voltar você será redirecionado para página de usuário onde poderá ver o crédito comprado acrescentado depois da compra.

![usuario_recarregado](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/usuario_recarregado.png)

### Scripts auxiliares
#### 1. Teste do webhook IPN
É necessário possuir um arquivo json com a estrutura da notificação POST enviada pelo Paypal para notificação do webhook IPN. Este arquivo deve estar no diretório configurado na variável de ambiente REQUESTS_DIR, para testar o webhook IPN acesse o script testes/webhook-ipn.php, passando o nome do arquivo json na url como query parâmetro nome, exemplo: http://localhost/paypal/testes/webhook-ipn.php?nome=log_14887.json

![teste_webhook_ipn](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/teste_webhook_ipn.png)

#### 2. Leitura de Log
Estando com o diretório de logs do webhook configurado na variável de ambiente LOGS_WEBHOOK_DIR, podemos visualizar os detalhes da execução das chamadas ao webhook. Para isso é necessário acessar o script testes/log-webhook.php, passando o id do log na url como query parâmetro log_id correspondente ao registro da tabela logs_webhook do banco de dados, exemplo: http://localhost/paypal/testes/log-webhook.php?log_id=18

![log_webhook](https://github.com/rodriguesrenato61/paypal-integracao-php/blob/main/prints/log_webhook.png)

