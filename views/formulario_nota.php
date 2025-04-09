<form id="formNota" action="../public/index.php?action=salvar" method="POST">
    <div class="form-group">
        <label>Responsável:</label>
        <input type="text" name="responsavel" required>
    </div>

    <div class="form-group">
        <label>Número da Nota:</label>
        <input type="text" name="numero_nota" required>
    </div>

    <div class="form-group">
        <label>Fornecedor:</label>
        <input type="text" name="fornecedor" required>
    </div>

    <div class="form-group">
        <label>Valor:</label>
        <input type="number" step="0.01" name="valor" required>
    </div>

    <div class="form-group">
        <label>Data de Emissão:</label>
        <input type="date" name="data_emissao" required>
    </div>

    <div class="form-group">
        <label>Condição de Pagamento:</label>
        <select name="condicao_pagamento" required>
            <option value="">Selecione</option>
            <option value="À Vista">À Vista</option>
            <option value="30 dias">30 dias</option>
            <option value="60 dias">60 dias</option>
        </select>
    </div>

    <button type="submit">Salvar Nota</button>
    <button type="button" onclick="window.location.href='index.php'" style="background: #0977d8; color: white; margin-left: 10px;">Voltar</button>
</form>
