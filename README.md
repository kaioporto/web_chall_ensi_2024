# web_chall_ensi_2024
Desafio de web do CTF do EnSI 2024

- O desafio consiste em tentar encontrar uma vulnerabilidade no sistema web. A vulnerabilidade é uma codificação insegura na funcionalidade de update de dados do usuário. O formulário aceita apenas atualizar informações de nome e email, porém o servidor aceita e atualiza outras informações do usuário.

- Dessa forma, ao interceptar a requisição POST e adicionar admin=true, por exemplo, o usuário passa a ser admin da aplicação.

- Na página do usuário, agora sendo admin, a flag é encontrada.
