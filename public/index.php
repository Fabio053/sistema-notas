<?php
// Inclua o Controller
// Caminho correto para incluir o arquivo do controller
require_once '../controllers/NotaFiscalController.php';

$controller = new NotaFiscalController();

$action = $_GET['action'] ?? 'listar';

switch ($action) {
    case 'listar':
        $controller->listarNotas();
        break;
    case 'formulario':
        $controller->exibirFormulario();
        break;
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->salvarNota();
        }
        break;
    default:
        echo "Ação inválida.";
        break;
}
