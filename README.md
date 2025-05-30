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
| ADM01              | **Administrador**  | Gerir Utilizadores	| Como Administrador, quero criar/editar/eliminar contas de utilizadores para controlar os acessos ao sistema.                                | Alta
| ADM02              | **Administrador**  | Configurar Categorias  | Como Administrador, quero definir categorias de produtos para organizar o  inventário.					  | Média
| ARM01              | **Funcionário**    | Atualizar Stock | Como Funcionário, quero ajustar manualmente as quantidades de produtos para manter o inventário atualizado.                                     | Alta
| ARM02              | **Funcionário**    | Registar Encomendas | Como Funcionário, quero inserir novas encomendas de fornecedores para atualizar o stock. 					  | Alta
| FOR01              | **Fornecedor**     | Confirmar Entregas | Como Fornecedor, quero marcar encomendas como entregues e enviar comprovativos para validação.				                                  | Alta
