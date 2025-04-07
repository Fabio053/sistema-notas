<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../conexao.php';

// Consulta SQL para ordenar os status
$sql = "SELECT 
            id,
            responsavel,
            numero_nota,
            fornecedor,
            valor,
            data_emissao,
            data_vencimento,
            numero_requisicao,
            numero_pedido,
            protocolo,
            CASE
                WHEN (TRIM(COALESCE(numero_requisicao, '')) = '' 
                      AND TRIM(COALESCE(numero_pedido, '')) = '' 
                      AND protocolo IS NULL) 
                    THEN 'Requisição Pendente'
                    
                WHEN (TRIM(COALESCE(numero_requisicao, '')) != '' 
                      AND TRIM(COALESCE(numero_pedido, '')) = '' 
                      AND protocolo IS NULL) 
                    THEN 'Pedido Pendente'
                    
                WHEN (TRIM(COALESCE(numero_requisicao, '')) != '' 
                      AND TRIM(COALESCE(numero_pedido, '')) != '' 
                      AND protocolo IS NULL) 
                    THEN 'Protocolo Pendente'
                    
                ELSE 'OK'
            END AS status_nota,
            CASE
                WHEN (TRIM(COALESCE(numero_requisicao, '')) = '' 
                      AND TRIM(COALESCE(numero_pedido, '')) = '' 
                      AND protocolo IS NULL) 
                    THEN 1
                    
                WHEN (TRIM(COALESCE(numero_requisicao, '')) != '' 
                      AND TRIM(COALESCE(numero_pedido, '')) = '' 
                      AND protocolo IS NULL) 
                    THEN 2
                    
                WHEN (TRIM(COALESCE(numero_requisicao, '')) != '' 
                      AND TRIM(COALESCE(numero_pedido, '')) != '' 
                      AND protocolo IS NULL) 
                    THEN 3
                    
                ELSE 4
            END AS prioridade_status
        FROM notas_fiscais
        ORDER BY prioridade_status ASC, data_emissao DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

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
    <!-- Alterado para abrir o modal -->
    <a href="#" id="btn-modal" class="btn-nova-nota">➕ Nova Nota</a>
    <a href="calendario.php" class="btn-cal" style="background-color: #3498db;">📅 Calendário</a>
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
    document.addEventListener('DOMContentLoaded', function () {
    // Seleciona o contêiner da tabela onde os botões de editar estão localizados
    const tabela = document.querySelector('table tbody');
    
    tabela.addEventListener('click', function(event) {
        if (event.target && event.target.matches('button.editar')) {
            event.preventDefault();
            
            // Obtém o ID da nota fiscal do atributo data-id
            const idNota = event.target.getAttribute('data-id');
            
            // Carrega o conteúdo de editar_nota.php dentro do modal
            fetch(`editar_nota.php?id=${idNota}`)
                .then(response => response.text())
                .then(data => {
                    // Insere o conteúdo de editar_nota.php dentro do modal-body
                    document.getElementById('modal-body').innerHTML = data;
                    
                    // Exibe o modal
                    document.getElementById('modal').style.display = 'block';
                    
                    // Desabilita a rolagem da página
                    document.body.classList.add('no-scroll');
                })
                .catch(error => {
                    console.error('Erro ao carregar o formulário de edição:', error);
                    document.getElementById('modal-body').innerHTML = '<p>Erro ao carregar o formulário de edição.</p>';
                    document.getElementById('modal').style.display = 'block';
                });
        }
    });

    // Abre o modal para adicionar nova nota
    document.getElementById('btn-modal').addEventListener('click', function(event) {
        event.preventDefault(); // Impede a navegação padrão

        // Carrega o conteúdo de formulario.php dentro do modal
        fetch('formulario.php')
            .then(response => response.text())
            .then(data => {
                // Insere o conteúdo dentro do modal-body
                document.getElementById('modal-body').innerHTML = data;
                
                // Exibe o modal
                document.getElementById('modal').style.display = 'block';
                
                // Desabilita a rolagem da página
                document.body.classList.add('no-scroll');
            })
            .catch(error => {
                console.error('Erro ao carregar o formulário:', error);
                document.getElementById('modal-body').innerHTML = '<p>Erro ao carregar o formulário.</p>';
                document.getElementById('modal').style.display = 'block';
            });
    });

    // Fechar o modal se clicar fora da área do modal
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
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['responsavel']) ?></td>
                    <td><?= htmlspecialchars($row['numero_nota']) ?></td>
                    <td><?= htmlspecialchars($row['fornecedor']) ?></td>
                    <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y', strtotime($row['data_emissao'])) ?></td>
                    <td>
                        <?= !empty($row['data_vencimento']) ? date('d/m/Y', strtotime($row['data_vencimento'])) : 'N/A' ?>
                    </td>
                    <td><?= $row['numero_requisicao'] ? htmlspecialchars($row['numero_requisicao']) : 'N/A' ?></td>
                    <td><?= $row['numero_pedido'] ? htmlspecialchars($row['numero_pedido']) : 'N/A' ?></td>
                    <td>
                        <?php if (!empty($row['protocolo']) && 
                                !in_array($row['protocolo'], ['0000-00-00', '1970-01-01'])): ?>
                            <?= date('d/m/Y', strtotime($row['protocolo'])) ?>
                        <?php else: ?>
                            <span class="data-invalida">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="status 
                            <?php 
                                if (strpos($row['status_nota'], 'Requisição Pendente') !== false) {
                                    echo 'status-pendente-requisicao';
                                } elseif (strpos($row['status_nota'], 'Pedido Pendente') !== false) {
                                    echo 'status-pendente-pedido';
                                } elseif (strpos($row['status_nota'], 'Protocolo Pendente') !== false) {
                                    echo 'status-pendente-protocolo';
                                } else {
                                    echo 'status-ok';
                                }
                            ?>">
                            <?= $row['status_nota'] ?>
                        </div>
                    </td>
                    <td>
                        <button class="acoes editar" data-id="<?= $row['id'] ?>">✏️</button>
                    </td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

</body>
</html>

<?php 
$conn->close();
?>