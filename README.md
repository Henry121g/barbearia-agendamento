# ✂ Sistema de Agendamento para Barbearia

Sistema web para clientes agendarem horários em uma barbearia e para os barbeiros acompanharem a agenda do dia.

<!-- 🔗 ADICIONE AQUI o link do site publicado -->
**🔗 Acesse online:** _[link do site publicado]_

<!-- 🖼 ADICIONE AQUI um print da página de agendamento (ex.: docs/agendamento.png) -->
![Página de agendamento](docs/agendamento.png)

---

## 🎯 Problema que resolve

Muitas barbearias ainda marcam horários por mensagem no WhatsApp ou em um caderno. Isso gera horários duplicados, mensagens sem resposta e dificuldade para saber quanto foi faturado no dia.

Com este sistema:
- o **cliente** vê os horários realmente livres e agenda sozinho, a qualquer hora;
- o **barbeiro** tem a agenda do dia organizada, marca atendimentos como concluídos ou cancelados e vê o faturamento.

## ✅ Funcionalidades

**Área do cliente**
- Lista de serviços com preço e duração
- Agendamento com escolha de serviço, barbeiro, data e horário
- Horários livres carregados automaticamente (JavaScript + endpoint JSON), respeitando:
  - horário de funcionamento e dias da semana configurados
  - duração do serviço (um serviço de 60 min não é oferecido se só há 30 min livres)
  - horários que já passaram no dia de hoje
- Bloqueio de agendamento duplo, inclusive quando duas pessoas tentam o mesmo horário ao mesmo tempo (transação com `SELECT ... FOR UPDATE`)

**Área do barbeiro (com login)**
- Agenda por dia, com filtro de data
- Marcar atendimento como concluído ou cancelado
- Total faturado no dia (atendimentos concluídos)
- Link direto para o WhatsApp do cliente
- Cadastro de serviços e ativar/desativar serviços

**Segurança**
- Senhas armazenadas com `password_hash` (bcrypt)
- Consultas com *prepared statements* (proteção contra SQL Injection)
- Saída escapada com `htmlspecialchars` (proteção contra XSS)
- Token CSRF em todos os formulários
- Dados do banco fora do código versionado (`config.php` no `.gitignore`)

<!-- 🖼 ADICIONE AQUI um print do painel do barbeiro (ex.: docs/painel.png) -->
![Painel do barbeiro](docs/painel.png)

## 🚀 Próximas melhorias

- [ ] Cadastro de barbeiros pelo painel (hoje é feito direto no banco)
- [ ] Horário de almoço e folgas individuais por barbeiro
- [ ] Cliente cancelar o próprio agendamento por um link
- [ ] Lembrete automático por WhatsApp ou e-mail
- [ ] Limite de tentativas de login (proteção contra força bruta)
- [ ] Testes automatizados (PHPUnit) para a regra de horários livres

## 🛠 Tecnologias

| Camada | Tecnologia |
|---|---|
| Back-end | PHP 8.1+ (sem framework) |
| Banco de dados | MySQL / MariaDB, acesso via PDO |
| Front-end | HTML5, CSS3 (Grid/Flexbox, responsivo) e JavaScript puro (`fetch`) |
| Servidor | Apache (XAMPP) ou servidor embutido do PHP |

## 📁 Estrutura

```
├── index.php            # Página pública de agendamento
├── agendar.php          # Recebe e valida o formulário
├── horarios.php         # Endpoint JSON com horários livres
├── admin/               # Área do barbeiro (login, agenda, serviços)
├── src/                 # Conexão, funções e regra de agenda (bloqueado ao navegador)
│   └── views/           # Cabeçalho e rodapé compartilhados
├── assets/              # CSS e JavaScript
├── scripts/             # Script de terminal para criar o administrador
├── database.sql         # Estrutura do banco + dados de exemplo
└── config.example.php   # Modelo de configuração
```

## 💻 Como rodar localmente

**Pré-requisitos:** PHP 8.1 ou superior e MySQL/MariaDB (o [XAMPP](https://www.apachefriends.org/) já traz os dois).

1. **Clone o repositório**
   ```bash
   git clone https://github.com/Henry121g/barbearia-agendamento.git
   cd barbearia-agendamento
   ```

2. **Crie o banco de dados** (com o MySQL ligado no painel do XAMPP)
   ```bash
   mysql -u root -p < database.sql
   ```
   Ou abra o phpMyAdmin → aba **Importar** → selecione `database.sql`.

3. **Configure a conexão**: copie o arquivo de exemplo e ajuste usuário/senha do seu MySQL
   ```bash
   cp config.example.php config.php
   ```

4. **Crie seu usuário administrador**
   ```bash
   php scripts/criar_admin.php seu_usuario sua_senha_forte
   ```

5. **Inicie o servidor**
   ```bash
   php -S localhost:8080
   ```
   Acesse <http://localhost:8080>. A área do barbeiro fica em <http://localhost:8080/admin/login.php>.

   > Usando o Apache do XAMPP: coloque a pasta em `C:\xampp\htdocs\` e acesse `http://localhost/nome-da-pasta`.

## 📚 O que aprendi

<!--
  ✍ Responda com as SUAS palavras. Recrutadores valorizam ver como você pensa,
  não uma lista perfeita. Apague as perguntas depois de responder.
-->

**1. Qual foi a parte mais difícil do projeto e como você resolveu?**
_Sua resposta aqui._

**2. Como funciona a lógica que descobre se um horário está livre? Explique como se fosse para alguém que não programa.**
_Sua resposta aqui._

**3. O que é SQL Injection e XSS, e o que neste projeto protege contra cada um?**
_Sua resposta aqui._

**4. Por que o `config.php` não vai para o GitHub e o `config.example.php` vai?**
_Sua resposta aqui._

**5. O que acontece se duas pessoas tentarem agendar o mesmo horário no mesmo segundo? Como o sistema evita o problema?**
_Sua resposta aqui._

**6. Se você fosse começar de novo, o que faria diferente?**
_Sua resposta aqui._

---

Desenvolvido por **Henry Gonçalves Furtuna**
<!-- 🔗 ADICIONE AQUI seu LinkedIn -->
[LinkedIn](_link_) · [GitHub](https://github.com/Henry121g)
