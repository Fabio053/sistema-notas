<?php
// public/atualizar_nota.php
session_start();
require_once '../controllers/NotaFiscalController.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) die("ID inválido");

$controller = new NotaFiscalController();
$controller->atualizarNota($_POST);
?>
