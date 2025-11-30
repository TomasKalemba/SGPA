<!-- BOTÃO VOLTAR PARA HOME -->
<div class="m-3">
    <a href="indexDocente.php" class="btn btn-outline-primary">
        <i class="fas fa-home"></i> Home
    </a>
</div>
<!-- FIM BOTÃO -->

            <script> 
               // Ao submeter o formulário de pesquisa
document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault();  // Evitar o envio do formulário tradicional

    var query = document.getElementById('searchQuery').value;

    // Enviar a pesquisa para o back-end via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?query=' + encodeURIComponent(query), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Exibir os resultados da pesquisa
            var resultsContainer = document.getElementById('resultsContainer');
            var searchResultsDiv = document.getElementById('searchResults');
            resultsContainer.innerHTML = xhr.responseText;  // Inserir os resultados retornados

            // Mostrar a div com os resultados
            searchResultsDiv.style.display = 'block';
        } else {
            console.error('Erro na requisição: ' + xhr.status);
        }
    };
    xhr.send();
});

// Fechar os resultados ao clicar no botão de fechar
document.getElementById('closeSearchResults').addEventListener('click', function() {
    document.getElementById('searchResults').style.display = 'none';  // Esconde a div com os resultados
});




            </script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebarToggle = document.getElementById("sidebarToggle");
        const body = document.body;
        const sidenav = document.getElementById("layoutSidenav_nav");

        sidebarToggle.addEventListener("click", function (e) {
            e.preventDefault();
            body.classList.toggle("sb-sidenav-toggled");

            // Salva preferência no localStorage (opcional)
            localStorage.setItem('sb|sidebar-toggle', body.classList.contains('sb-sidenav-toggled'));
        });

        // Restaura a preferência ao carregar
        if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
            body.classList.add('sb-sidenav-toggled');
        }
    });
</script>
</script>
