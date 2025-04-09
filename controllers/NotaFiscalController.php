<?php
require_once '../models/NotaFiscalModel.php';

class NotaFiscalController {

    public function buscarNotasPorData($data_vencimento) {
        $model = new NotaFiscalModel();

        if (empty($data_vencimento)) {
            http_response_code(400);
            echo json_encode(['error' => 'Data de vencimento não fornecida.']);
            return;
        }

        $resultado = $model->buscarPorDataVencimento($data_vencimento);

        header('Content-Type: application/json');

        if (isset($resultado['error'])) {
            http_response_code(500);
            echo json_encode($resultado);
        } else {
            echo json_encode($resultado);
        }
    }

    public function listarNotas() {
        $model = new NotaFiscalModel();
        $notas = $model->getNotas();
        include '../views/listar_notas.php';
    }

    public function exibirFormulario() {
        $erro = '';
        include '../views/formulario_nota.php';
    }

    public function salvarNota() {
        $model = new NotaFiscalModel();

        $dados = [
            'responsavel' => $_POST['responsavel'],
            'numero_nota' => $_POST['numero_nota'],
            'fornecedor' => $_POST['fornecedor'],
            'valor' => $_POST['valor'],
            'data_emissao' => $_POST['data_emissao'],
            'condicao_pagamento' => $_POST['condicao_pagamento'],
            'numero_requisicao' => null,
            'numero_pedido' => null,
            'protocolo' => null
        ];

        $resultado = $model->inserirNota($dados);

        if ($resultado) {
            header("Location: index.php");
            exit();
        } else {
            $erro = "Erro ao salvar a nota.";
            include '../views/formulario_nota.php';
        }
    }

    public function atualizarNota($dados) {
        require_once '../models/NotaFiscalModel.php';
    
        $model = new NotaFiscalModel();
    
        try {
            $resultado = $model->atualizarNota($dados);
            $_SESSION['msg'] = "✅ Nota atualizada com sucesso!";
        } catch (Exception $e) {
            $_SESSION['msg'] = "❌ Erro: " . $e->getMessage();
        }
    
        header("Location: ../public/index.php?id=" . $dados['id']);
        exit();
    }
    
}
