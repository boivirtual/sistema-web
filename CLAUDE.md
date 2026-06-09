# CLAUDE.md — Instruções para o Assistente

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
git push origin main
```

Nunca deixe alterações sem fazer o push. Cada sessão de trabalho deve
terminar com o repositório GitHub atualizado.

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
