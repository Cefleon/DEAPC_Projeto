# DEAPC_Projeto

## Membros do Grupo

- [André Tavares, nº1222016]
- [Fernando Martins, nº1232091]
- [Tiago Magalhães, nº1241742]

## Objetivos da Aplicação

Este projeto consiste em um **Sistema de Gestão de Inventário** com as seguintes funcionalidades:

- Gestão de níveis de stock em tempo real.
- Processamento de encomendas e acompanhamento do seu cumprimento.
- Interação com um servidor de base de dados para armazenar e recuperar dados de inventário.
- Monitorização de compras e alertas de reabastecer stock.

## Tipos de Utilizadores

| Tipo               | Descrição                                                                 | Permissões                                                                 |
|--------------------|---------------------------------------------------------------------------|----------------------------------------------------------------------------|
| **Administrador**  | Responsável pela gestão global do sistema.                                | - Criar/eliminar utilizadores<br>- Configurar categorias de produtos<br>- Acessar todos os relatórios |
| **Funcionário**    | Utilizador operacional (armazém ou loja).                                | - Atualizar stock<br>- Registar encomendas<br>- Gerar relatórios básicos   |
| **Fornecedor**     | Acesso externo para confirmar entregas.                                  | - Ver encomendas pendentes<br>- Confirmar entregas                         |


## User Stories

| Código             | Tipo               | Nome			| Descrição                                                                 | Prioridade
|--------------------|--------------------|-----------------------------|---------------------------------------------------------------------------|--------------------------------
| ADM01              | **Administrador**  | Aceder a toda a informação	| Pretende aceder à informação global do sistema, para proceder à gestão do mesmo.                                | Alta
| ADM02              | **Administrador**  | Aceder a toda a informação  | Pretende aceder à informação global do sistema, para proceder à gestão do mes					  | Alta
| USR01              | **Funcionário**    | Aceder a informação relativa a encomendas | Pretende efectuar processamento de encomendas, acompanhamento                                     | Média
| USR02              | **Funcionário**    | Aceder a informação relativa a encomendas | Pretende efectuar processamento de encomendas, acompanhamento 					  | Média
| USR03              | **Funcionário**    | Aceder a informação relativa a encomendas | Pretende efectuar processamento de encomendas, acompanhamento					  | Média 
| DIS01              | **Fornecedor**     | Aceder a informação relativa a compras | Pretende efectuar processamento de compras				                                  | Baixa
| DIS02              | **Fornecedor**     | Aceder a informação relativa a compras | Pretende efectuar processamento de compras								  | Baixa
