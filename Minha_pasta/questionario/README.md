# Questionário de Educação Física

Sistema de questionário online para avaliação de conhecimentos em Educação Física.

## 📝 Estrutura do Questionário

O questionário é dividido em 3 partes:

### 1. Questões de Múltipla Escolha (4 questões)
- Q1: Objetivos do aquecimento
- Q2: Benefícios da atividade física regular
- Q3: Características dos esportes coletivos
- Q4: Recomendações da OMS para adolescentes

### 2. Questões Dissertativas (4 questões)
- Q5: Importância da alimentação equilibrada
- Q6: Papel da Educação Física Escolar
- Q7: Diferença entre atividade física e exercício físico
- Q8: Importância do descanso e recuperação

### 3. Questões de Verdadeiro ou Falso (8 afirmações)
Organizadas em 4 grupos de 2 questões cada, com opções de Verdadeiro/Falso via radio buttons.

## 🗄️ Banco de Dados

### Configuração
1. Crie um banco de dados chamado `questionario`
2. Execute o arquivo SQL: `questionario_educacao_fisica.sql`

### Estrutura da Tabela
```sql
CREATE TABLE pesquisa_educacao_fisica (
    codigo INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NULL,
    setor VARCHAR(30) NULL,
    cargo VARCHAR(30) NULL,
    cpf VARCHAR(14) NULL,
    aquecimento VARCHAR(1) NULL,
    beneficio_atividade VARCHAR(1) NULL,
    esportes_coletivos VARCHAR(1) NULL,
    frequencia_oms VARCHAR(1) NULL,
    alimentacao_atividade TEXT NULL,
    educacao_fisica_escolar TEXT NULL,
    diferenca_atividade_exercicio TEXT NULL,
    descanso_recuperacao TEXT NULL,
    vf_1 VARCHAR(1) NULL,
    vf_2 VARCHAR(1) NULL,
    vf_3 VARCHAR(1) NULL,
    vf_4 VARCHAR(1) NULL,
    vf_5 VARCHAR(1) NULL,
    vf_6 VARCHAR(1) NULL,
    vf_7 VARCHAR(1) NULL,
    vf_8 VARCHAR(1) NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

## 🚀 Instalação

### Requisitos
- PHP 7.0 ou superior
- MySQL/MariaDB
- Servidor web (Apache, Nginx, etc.)

### Passos
1. Copie todos os arquivos para o diretório do servidor web
2. Configure o banco de dados executando o arquivo SQL
3. Ajuste as configurações de conexão em `conecta.php` se necessário
4. Acesse o formulário através do navegador

## 📁 Arquivos do Sistema

- `index.php` - Formulário do questionário
- `gravar.php` - Processamento dos dados
- `conecta.php` - Configuração de conexão com banco de dados
- `questionario_educacao_fisica.sql` - Estrutura do banco de dados

## 🔧 Configuração do Banco de Dados

O arquivo `conecta.php` vem configurado com:
- Host: localhost
- Usuário: root
- Senha: (vazia)
- Banco de dados: questionario

Ajuste essas configurações conforme necessário para o seu ambiente.