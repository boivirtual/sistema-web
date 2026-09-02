# CLAUDE.md — Instruções para o Assistente

## Premissa obrigatória nº 1: Idioma

Todas as respostas, explicações, mensagens e textos escritos pelo assistente
devem ser **obrigatoriamente em Português do Brasil**, independentemente do
idioma usado na pergunta. Isso inclui mensagens de commit, comentários de
resposta e qualquer texto exibido ao usuário. Código, nomes de variáveis e
termos técnicos que já estejam em inglês por convenção do sistema podem
permanecer em inglês.

---

## Premissa obrigatória nº 2: Uma branch só — `master`, sempre, incluindo `api/`

Este repositório (`sistema-web`) contém o código do **sistema web** em PHP
**e também** a pasta `api/`, que é o backend consumido pelo aplicativo
mobile Flutter (`boivirtual/aplicativo-mobile`, pasta local
`C:\Users\George\Desktop\boivirtual`). **Todo commit de arquivo deste
repositório — sistema web OU `api/` — deve ser feito exclusivamente na
branch `master`.** Não existe mais uma branch separada para `api/`.

**Histórico:** até 2026-09-02 existia uma branch `offline-pesagem` neste
repositório, pensada só para os arquivos de `api/` relacionados ao app.
Isso causou confusão de verdade: o deploy manual (ver regra abaixo) sempre
publica o que está no `master`, então o trabalho feito só na
`offline-pesagem` (idempotência por `uuid_app` em `PesagemDao.php`, o
endpoint `api/rest/animal/list_fazenda_completo.php`, entre outros) nunca
chegou a ir pro ar de verdade, mesmo já pronto e testado — o servidor
seguia rodando a versão antiga do `master`, sem essas correções. Por isso
essa premissa mudou: `api/` volta a viver só no `master`, junto com o
resto. A branch `offline-pesagem` não deve mais receber commits nem ser
usada como referência.

**Importante sobre o deploy**: o George **não usa git para publicar no
servidor de teste** (`agrolandes.com.br/teste_reproducao`) — o envio é
manual, direto desta pasta local pro servidor via FileZilla (arrastando os
arquivos que estão em disco agora). Por isso é essencial que **esta pasta
esteja sempre com a branch `master` ativa e atualizada** — qualquer código
numa branch diferente simplesmente nunca vai pro ar, não importa o quanto
esteja pronto.

---

## Projeto
**Nome:** Boi Virtual — Sistema Web  
**Tecnologias:** PHP, MySQL, Bootstrap 3, jQuery, DataTables, Font Awesome 5  
**Servidor local:** WAMP (Apache + MySQL)  
**Repositório:** https://github.com/boivirtual/sistema-web.git

---

## Regra obrigatória: Atualizar GitHub após toda alteração

Sempre que qualquer arquivo for criado, editado ou excluído nesta pasta,
execute obrigatoriamente os comandos abaixo antes de encerrar a tarefa:

```bash
cd "C:\wamp64\www\reproducao\sistema"
git add -A
git commit -m "descrição resumida do que foi alterado"
git push origin master
```

Nunca deixe alterações sem fazer o push. Cada sessão de trabalho deve
terminar com o repositório GitHub atualizado.

**Importante:** todo o trabalho deste projeto acontece na branch `master`
(é ela que está atualizada e é usada para as releases). A branch `main`
existe no repositório remoto mas está desatualizada e não deve receber
commits nem pushes — deixe-a como está. Veja também a **Premissa
obrigatória nº 2** acima sobre a branch `offline-pesagem`: existe um hook
automático que roda `git commit` + `git push origin master` a cada
edição/criação de arquivo, mas ele commita na branch que estiver ativa no
momento — por isso é essencial confirmar que a branch ativa é `master`
antes de editar qualquer arquivo do sistema web.

---

## Padrões do sistema (seguir sempre)

### Bootstrap
- Versão: **Bootstrap 3** (não usar classes do Bootstrap 4/5)
- Botão principal: `btn btn-primary`
- Botão secundário: `btn btn-info`
- Botão cancelar/fechar: `btn btn-default`

### Ícones
- Biblioteca: **Font Awesome 5** (`fas`, `far`, `fa`)

### Formulários
- Seções: `<fieldset class="scheduler-border">` + `<legend class="scheduler-border fonte-legend">`
- Selects com busca: classe `selectpicker` com atributo `data-live-search="true"`
- Labels: classe `control-label`

### Modais
- Sempre usar `data-backdrop="static"` (não fecha ao clicar fora)
- Estrutura padrão: `modal-header` + `modal-body` + `modal-footer`

### JavaScript
- Lógica separada em arquivos `.js` dentro da pasta `js/`
- Chamadas AJAX para arquivos PHP de backend separados

### Segurança
- **Nunca** commitar `conecta_mysql.inc` ou arquivos com credenciais
- **Nunca** usar `SELECT *` em queries novas — especificar os campos
- Sempre usar `mysqli_real_escape_string` ou prepared statements em inputs do usuário

---

## Estrutura de arquivos relevante

```
/sistema
├── form_contas_pagar.php          # Tela principal - listagem
├── form_contas_pagar_incluir.php  # Inclusão de nova conta
├── form_contas_pagar_editar.php   # Edição de conta existente
├── js/contas_pagar.js             # JavaScript do módulo
├── api/                           # Endpoints AJAX
├── css/style.css                  # Estilos customizados
└── CLAUDE.md                      # Este arquivo
```

---

## Banco de dados
- Conexão: `conecta_mysql.inc` (não versionado)
- Prefixo das tabelas: `tbl_`
- Campos com lixeira lógica: `*_lixeira = 0` significa ativo
