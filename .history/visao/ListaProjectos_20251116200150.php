<script>
    $(document).ready(function () {
        // Inicializando o DataTable
        var table = $('.tabela-projetos').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Projetos'
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Projetos',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    title: 'Projetos'
                }
            ],
            language: {
                search: "Pesquisar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Nenhum registro encontrado",
                zeroRecords: "Nenhum registro correspondente encontrado",
                paginate: {
                    first: "Primeiro",
                    last: "Último",
                    next: "Próximo",
                    previous: "Anterior"
                },
            }
        });

        // Função para adicionar um novo projeto via AJAX
        $('#adicionarProjetoForm').submit(function(e) {
            e.preventDefault(); // Evita o envio padrão do formulário

            var formData = $(this).serialize(); // Coleta os dados do formulário

            $.ajax({
                url: '../controle/NovoProjeto.php', // Caminho para o script PHP que vai inserir o projeto
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert('Erro ao adicionar projeto: ' + response.error);
                    } else {
                        // Adiciona o novo projeto à tabela
                        var projeto = response;
                        table.row.add([
                            projeto.id, // ID do projeto
                            projeto.titulo, // Título
                            projeto.descricao, // Descrição
                            projeto.grupo, // Grupo (se necessário)
                            projeto.prazo, // Prazo
                            '<a href="download.php?file=' + projeto.arquivo + '" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>', // Link para download
                            '<a href="../visao/Editar.php?id=' + projeto.id + '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>' +
                            '<a href="../controlo/EliminarProjecto.php?id=' + projeto.id + '" class="btn btn-danger btn-sm" onclick="return confirm(\'Tem certeza que deseja eliminar este projeto?\');"><i class="fas fa-trash-alt"></i></a>' // Ações de editar e excluir
                        ]).draw(false); // Redesenha a tabela com o novo projeto
                    }
                },
                error: function() {
                    alert('Erro ao enviar o formulário');
                }
            });
        });
    });
</script>
