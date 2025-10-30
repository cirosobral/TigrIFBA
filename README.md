# 🐅 TigrIFBA

**TigrIFBA** é uma plataforma unificada para jogos ~~de azar~~ com resultados aleatórios com fins educativos, voltada ao ensino de análise e desenvolvimento de sistemas.

O sistema possui uma **área pública**, composta de um **dashboard** e uma **interface administrativa**; e uma **API** que permite verificar o saldo e registrar operações de crédito e débito no saldo do usuário, permitindo assim que diferentes equipes desenvolvam jogos e módulos independentes que se comunicam com a mesma base de dados.

---

## 🚀 Estrutura do Projeto

````

TigrIFBA/
├── data/
│ └── init_db.php   # Script para inicializar o banco de dados
|
├── public/         # Área pública da aplicação (interface do usuário e painel administrativo)
│ ├── admin/        # Painel administrativo
| | └── ...
│ ├── assets/       # Imagens, CSS, JS e outros recursos estáticos
| │ └── ...
│ ├── dashboard.php # Página do dashboard
│ ├── index.php     # Página inicial (redireciona para login.php ou dashboard.php)
│ ├── login.php     # Página de login
│ ├── logout.php    # Página de logout
│ ├── play.php      # Página de jogo
│ ├── register.php  # Página de registro
│ ├── swagger.html  # Página do Swagger UI
│ └── swagger.yaml  # Documentação da API (OpenAPI)
│
├── src/
│ ├── api.php       # Script principal da API
│ ├── auth.php      # Script auxiliar sobre autenticação
│ └── db.php        # Script auxiliar sobre banco de dados
|
└── README.md

````

---

## ⚙️ Pré-requisitos

Antes de rodar o projeto, certifique-se de ter instalado:

- [PHP 8.1+](https://www.php.net/downloads)

- [SQLite](https://www.sqlite.org/)

- Navegador moderno (Chrome, Firefox, Edge, etc.)

---

## 🧩 Configuração do Ambiente

1.  **Clone o repositório:**

```bash

git clone https://github.com/cirosobral/TigrIFBA.git

cd TigrIFBA

```

2.  **Configure o banco de dados:**

* Execute o script de inicialização em `data/init_db.php`:

```bash

php data/init_db.php

```

---

## 🖥️ Executando o Projeto

O TigrIFBA utiliza o servidor PHP em dois ambientes:

### 1️⃣ Servidor Público (Interface e Painel Admin)

Para rodar a interface principal do sistema (acessível no navegador):

```bash

php  -S  localhost:8000  -t  public

```

Abra em seu navegador:

👉 [http://localhost:8000](http://localhost:8000)

Para acessar a descrição da API com Swagger UI:

Abra em seu navegador:

👉 [http://localhost:8000/swagger.html](http://localhost:8000/swagger.html)

---

### 2️⃣ Servidor da API

A API é o back-end responsável por centralizar as operações de banco de dados, permitindo a comunicação entre módulos:

```bash

php  -S  localhost:3000  src/api.php

```

Acesse (para testar, por exemplo, o endpoint `/status`):

👉 [http://localhost:3000/status](http://localhost:3000/status)

---

## 🧑‍💻 Contribuição

Contribuições são bem-vindas!

Para colaborar:

1. Faça um fork do repositório

2. Crie uma branch para sua feature:

```bash

git checkout -b feature/nova-funcionalidade

```

3. Faça commit das suas alterações:

```bash

git commit -m "Adiciona nova funcionalidade"

```

4. Envie para o seu repositório:

```bash

git push origin feature/nova-funcionalidade

```

5. Abra um Pull Request

---

## 📚 Créditos

Desenvolvido por [**Ciro Sobral**](https://github.com/cirosobral) para estudantes do **Instituto Federal da Bahia (IFBA)**

Projeto: **TigrIFBA — Tecnologia Integrada para Gamificação e Recursos Interativos**