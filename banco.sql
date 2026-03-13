CREATE TABLE usuarios(
    id INTEGER AUTO_INCREMENT NOT NULL,
    username VARCHAR(35),
    email VARCHAR(75),
    nome VARCHAR(150),
    profissao VARCHAR(100),
    creditos INTEGER DEFAULT 0,
    data_criacao DATETIME,
    data_atualizacao DATETIME,
    PRIMARY KEY(id)
);

INSERT INTO usuarios(id, username, email, nome, profissao, creditos, data_criacao)
    VALUES(1, 'rodriguesrenato', 'rrodrigues.dev01@gmail.com', 'Renato Rodrigues', 'Desenvolvedor Web', 0, NOW());

CREATE TABLE pacotes(
    id INTEGER AUTO_INCREMENT NOT NULL,
    nome VARCHAR(150),
    valor DECIMAL(10,2),
    creditos INTEGER,
    data_criacao DATETIME,
    data_atualizacao DATETIME,
    PRIMARY KEY(id)
);

INSERT INTO pacotes(id, nome, valor, creditos, data_criacao)
    VALUES(1, 'Pacote de 1.000 de créditos - 10 reais', 10.00, 1000, NOW()),
    (2, 'Pacote de 2.000 de créditos - 20 reais', 20.00, 2000, NOW()),
    (3, 'Pacote de 5.000 de créditos - 50 reais', 50.00, 5000, NOW()),
    (4, 'Pacote de 10.000 de créditos - 100 reais', 100.00, 10000, NOW());

CREATE TABLE paypal_credenciais(
    id INTEGER AUTO_INCREMENT NOT NULL,
    access_token LONGTEXT,
    token_tipo VARCHAR(30),
    data_expiracao DATETIME,
    ambiente ENUM('sandbox','production'),
    data_criacao DATETIME,
    data_atualizacao DATETIME,
    PRIMARY KEY(id)
);

INSERT INTO paypal_credenciais(id, access_token, token_tipo, data_expiracao, ambiente, data_criacao)
    VALUES(1, NULL, NULL, NULL, 'sandbox', '2026-03-10 07:00:00'),
    (2, NULL, NULL, NULL, 'production', '2026-03-10 07:00:00');

CREATE TABLE paypal_compras(
    id INTEGER AUTO_INCREMENT NOT NULL,
    tipo ENUM('ipn','order'),
    usuario_id INTEGER,
    pacote_id INTEGER,
    descricao VARCHAR(150),
    creditos INTEGER,
    currency_code VARCHAR(10),
    valor DECIMAL(10,2),
    valor_pago DECIMAL(10,2) DEFAULT 0.00,
    payment_status VARCHAR(50),
    invoice_id VARCHAR(50),
    txn_id VARCHAR(30),
    order_id VARCHAR(50),
    ambiente ENUM('sandbox','production'),
    codigo_referencia VARCHAR(35),
    data_criacao DATETIME,
    data_atualizacao DATETIME,
    PRIMARY KEY(id)
);

ALTER TABLE paypal_compras ADD CONSTRAINT FK_PAYPAL_COMPRA_USUARIO FOREIGN KEY(usuario_id) REFERENCES usuarios(id);
ALTER TABLE paypal_compras ADD CONSTRAINT FK_PAYPAL_COMPRA_PACOTE FOREIGN KEY(pacote_id) REFERENCES pacotes(id);

CREATE VIEW vw_paypal_compras AS SELECT pc.id, pc.tipo, pc.usuario_id, u.username, u.nome AS usuario_nome, pc.pacote_id, pc.descricao, pc.creditos, pc.currency_code, pc.valor, pc.valor_pago, pc.payment_status, pc.invoice_id, pc.txn_id, pc.order_id, pc.ambiente, pc.codigo_referencia, pc.data_criacao, DATE_FORMAT(pc.data_criacao, '%d/%m/%Y %H:%i:%s') AS data_criacao_br, pc.data_atualizacao, DATE_FORMAT(pc.data_atualizacao, '%d/%m/%Y %H:%i:%s') AS data_atualizacao_br
FROM paypal_compras pc
INNER JOIN usuarios u ON pc.usuario_id = u.id;

CREATE TABLE logs_creditos(
    id INTEGER AUTO_INCREMENT NOT NULL,
    usuario_id INTEGER,
    paypal_compra_id INTEGER,
    credito_anterior INTEGER,
    credito_posterior INTEGER,
    data_criacao DATETIME,
    PRIMARY KEY(id)
);

ALTER TABLE logs_creditos ADD CONSTRAINT FK_LOG_CREDITO_USUARIO FOREIGN KEY(usuario_id) REFERENCES usuarios(id);
ALTER TABLE logs_creditos ADD CONSTRAINT FK_LOG_CREDITO_PAYPAL_COMPRA FOREIGN KEY(paypal_compra_id) REFERENCES paypal_compras(id);

CREATE TABLE logs_webhook(
    id INTEGER AUTO_INCREMENT NOT NULL,
    mensagem VARCHAR(500),
    sucesso ENUM('S','N') DEFAULT 'N',
    paypal_compra_id INTEGER,
    arquivo VARCHAR(75),
    data_criacao DATETIME,
    PRIMARY KEY(id)
);