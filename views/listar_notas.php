<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Notas Fiscais</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/modal.css">
</head>
<body>

<div class="header">
    <h1>Gestão de Notas Fiscais</h1>
    <!-- Link para adicionar nova nota -->
    <a href="#" id="btn-modal" class="btn-nova-nota">➕ Nova Nota</a>
    <a href="../public/calendario.php" class="btn-cal" style="background-color: #3498db;">📅 Calendário</a>
</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span id="close-modal" class="close">&times;</span>
        <div id="modal-body">
            <!-- O conteúdo do formulário será inserido aqui via JavaScript -->
        </div>
    </div>
</div>

<script>
    // Script para gerenciar o modal de edição e criação de notas
    document.addEventListener('DOMContentLoaded', function () {
        const tabela = document.querySelector('table tbody');

        tabela.addEventListener('click', function(event) {
            if (event.target && event.target.matches('button.editar')) {
                event.preventDefault();

                const idNota = event.target.getAttribute('data-id');

                fetch(`editar_nota.php?id=${idNota}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('modal-body').innerHTML = data;
                        document.getElementById('modal').style.display = 'block';
                        document.body.classList.add('no-scroll');
                    })
                    .catch(error => {
                        console.error('Erro ao carregar o formulário de edição:', error);
                        document.getElementById('modal-body').innerHTML = '<p>Erro ao carregar o formulário de edição.</p>';
                    });
            }
        });

        document.getElementById('btn-modal').addEventListener('click', function(event) {
            event.preventDefault();

            fetch('index.php?action=formulario')

                .then(response => response.text())
                .then(data => {
                    document.getElementById('modal-body').innerHTML = data;
                    document.getElementById('modal').style.display = 'block';
                    document.body.classList.add('no-scroll');
                })
                .catch(error => {
                    console.error('Erro ao carregar o formulário:', error);
                    document.getElementById('modal-body').innerHTML = '<p>Erro ao carregar o formulário.</p>';
                });
        });

        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('modal')) {
                document.getElementById('modal').style.display = 'none';
                document.body.classList.remove('no-scroll');
            }
        });
    });
</script>

<table>
    <thead>
        <tr>
            <th>Responsável</th>
            <th>Número</th>
            <th>Fornecedor</th>
            <th>Valor</th>
            <th>Emissão</th>
            <th>Vencimento</th>
            <th>Requisição</th>
            <th>Pedido</th>
            <th>Protocolo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $notas->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['responsavel']) ?></td>
            <td><?= htmlspecialchars($row['numero_nota']) ?></td>
            <td><?= htmlspecialchars($row['fornecedor']) ?></td>
            <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
            <td><?= date('d/m/Y', strtotime($row['data_emissao'])) ?></td>
            <td><?= !empty($row['data_vencimento']) ? date('d/m/Y', strtotime($row['data_vencimento'])) : 'N/A' ?></td>
            <td><?= $row['numero_requisicao'] ? htmlspecialchars($row['numero_requisicao']) : 'N/A' ?></td>
            <td><?= $row['numero_pedido'] ? htmlspecialchars($row['numero_pedido']) : 'N/A' ?></td>
            <td><?= !empty($row['protocolo']) ? date('d/m/Y', strtotime($row['protocolo'])) : 'N/A' ?></td>
            <td><?= $row['status_nota'] ?></td>
            <td>
                <button class="acoes editar" data-id="<?= $row['id'] ?>">✏️</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
