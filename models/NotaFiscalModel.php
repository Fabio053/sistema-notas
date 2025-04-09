<?php
class NotaFiscalModel {
    
    private $conn;

    public function __construct() {
        require_once '../conexao.php';
        $this->conn = $conn;
    }

    public function getNotas() {
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
                    status_nota,
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

        $result = $this->conn->query($sql);

        if (!$result) {
            die("Erro na consulta: " . $this->conn->error);
        }

        return $result;
    }

    public function inserirNota($dados) {
        $stmt = $this->conn->prepare("INSERT INTO notas_fiscais 
            (responsavel, numero_nota, fornecedor, valor, data_emissao, condicao_pagamento, numero_requisicao, numero_pedido, protocolo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssdsssss",
            $dados['responsavel'],
            $dados['numero_nota'],
            $dados['fornecedor'],
            $dados['valor'],
            $dados['data_emissao'],
            $dados['condicao_pagamento'],
            $dados['numero_requisicao'],
            $dados['numero_pedido'],
            $dados['protocolo']
        );

        return $stmt->execute();
    }

    public function buscarPorDataVencimento($data_vencimento) {
        $stmt = $this->conn->prepare("SELECT numero_nota, status_nota FROM notas_fiscais WHERE data_vencimento = ?");
        
        if (!$stmt) {
            return ['error' => 'Erro ao preparar a consulta: ' . $this->conn->error];
        }

        $stmt->bind_param('s', $data_vencimento);
        $stmt->execute();
        $result = $stmt->get_result();

        $notas = [];
        while ($row = $result->fetch_assoc()) {
            $notas[] = $row;
        }

        $stmt->close();
        return $notas;
    }
    
    public function atualizarNota($dados) {
    $id = (int)$dados['id'];

    $valor = (float) str_replace(',', '.', $dados['valor']);
    if ($valor > 99999999.99 || $valor <= 0) {
        throw new Exception("Valor inválido! Deve ser entre R$ 0,01 e R$ 99.999.999,99");
    }

    $responsavel = !empty($dados['responsavel']) ? $dados['responsavel'] : null;
    $numero_nota = !empty($dados['numero_nota']) ? $dados['numero_nota'] : null;
    $fornecedor = !empty($dados['fornecedor']) ? $dados['fornecedor'] : null;
    $data_emissao = !empty($dados['data_emissao']) ? $dados['data_emissao'] : null;
    $condicao_pagamento = !empty($dados['condicao_pagamento']) ? $dados['condicao_pagamento'] : null;
    $numero_requisicao = !empty($dados['numero_requisicao']) ? $dados['numero_requisicao'] : null;
    $numero_pedido = !empty($dados['numero_pedido']) ? $dados['numero_pedido'] : null;
    $protocolo = !empty($dados['protocolo']) ? $dados['protocolo'] : null;

    if ($protocolo) {
        $timestamp = strtotime($protocolo);
        if (!$timestamp) {
            throw new Exception("Formato de data do protocolo inválido!");
        }
        $protocolo = date('Y-m-d', $timestamp);
    }

    if ($data_emissao) {
        $timestamp = strtotime($data_emissao);
        if (!$timestamp) {
            throw new Exception("Formato de data de emissão inválido!");
        }
        $data_emissao = date('Y-m-d', $timestamp);
    }

    $data_vencimento = null;
    if (!empty($data_emissao) && !empty($condicao_pagamento)) {
        $prazo_pagamento = intval(preg_replace('/[^0-9]/', '', $condicao_pagamento));
        $data_vencimento = date('Y-m-d', strtotime("+$prazo_pagamento days", strtotime($data_emissao)));
    }

    $stmt = $this->conn->prepare("UPDATE notas_fiscais SET
        responsavel = ?, numero_nota = ?, fornecedor = ?, valor = ?, data_emissao = ?,
        condicao_pagamento = ?, numero_requisicao = ?, numero_pedido = ?, protocolo = ?, data_vencimento = ?
        WHERE id = ?");

    $stmt->bind_param(
        "sssdssssssi",
        $responsavel,
        $numero_nota,
        $fornecedor,
        $valor,
        $data_emissao,
        $condicao_pagamento,
        $numero_requisicao,
        $numero_pedido,
        $protocolo,
        $data_vencimento,
        $id
    );

    if (!$stmt->execute()) {
        throw new Exception("Erro na atualização: " . $stmt->error);
    }

    $stmt->close();
    return true;
}

}
