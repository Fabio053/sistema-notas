<?php
// Exibir erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../controllers/NotaFiscalController.php';

$data_vencimento = $_GET['data_vencimento'] ?? null;

$controller = new NotaFiscalController();
$controller->buscarNotasPorData($data_vencimento);
