# priorize
A WordPress plugin for colaborative priorization. A (very) simpler version of pairwise

this is work in progress

## Basic usage

QUando você ativar o plugin, um post type chamado Opções vai ser criado. Esse post type tem uma taxonmia chama Perguntas.

Crie uma nova pergunta e então várias opções para cada pergunta, sempre marcando ao criar um novo post do tipo "opção" a qual "pergunta" ela pertence.

Para inserir a pergunta no front-end, use a função priorize_print_pergunta($id_da_pergunta), onde o $id_da_pergunta é o ID do termo da taxonomia que você criou.

* Usuários não logados podem votar
* Usuários não logados não podem sugerir novas opções (são direcionados pra página de login)
* Novas opções entram como rascunho
