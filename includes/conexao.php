<?php
/**
 * Conexão com o banco de dados do projeto Calendário SENAC.
 * Ajuste as credenciais conforme o seu ambiente do XAMPP.
 */

if (!function_exists('conectarBanco')) {
    function conectarBanco()
    {
        static $conn = null;

        if ($conn instanceof mysqli) {
            return $conn;
        }

        $host = 'localhost';
        $usuario = 'root';
        $senha = '';
        $banco = 'Calendario';

        $conn = new mysqli($host, $usuario, $senha, $banco);

        if ($conn->connect_error) {
            die('Erro ao conectar ao banco de dados: ' . $conn->connect_error);
        }

        $conn->set_charset('utf8');

        return $conn;
    }
}

if (!function_exists('obterConfiguracao')) {
    function obterConfiguracao(string $chave, $padrao = null)
    {
        $conn = conectarBanco();

        $stmt = $conn->prepare('SELECT valor FROM configuracoes WHERE chave = ? LIMIT 1');
        if (!$stmt) {
            return $padrao;
        }

        $stmt->bind_param('s', $chave);
        $stmt->execute();
        $stmt->bind_result($valor);

        if ($stmt->fetch()) {
            $stmt->close();
            return $valor;
        }

        $stmt->close();
        return $padrao;
    }
}

if (!function_exists('obterEventosCalendario')) {
    function obterEventosCalendario(string $ano): array
    {
        $conn = conectarBanco();

        $inicioAno = "$ano-01-01";
        $fimAno = "$ano-12-31";

        $sql = '
            SELECT id, titulo, descricao, data_inicio, data_fim, tipo, cor
            FROM eventos
            WHERE
                (data_inicio BETWEEN ? AND ?)
                OR (data_fim BETWEEN ? AND ?)
                OR (data_inicio <= ? AND (data_fim IS NULL OR data_fim >= ?))
        ';

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('ssssss', $inicioAno, $fimAno, $inicioAno, $fimAno, $fimAno, $inicioAno);
        $stmt->execute();
        $stmt->bind_result($id, $titulo, $descricao, $data_inicio, $data_fim, $tipo, $cor);

        $eventos = [];
        while ($stmt->fetch()) {
            $eventos[] = [
                'id' => $id,
                'titulo' => $titulo,
                'descricao' => $descricao,
                'data_inicio' => $data_inicio,
                'data_fim' => $data_fim,
                'tipo' => $tipo,
                'cor' => $cor
            ];
        }

        $stmt->close();
        return $eventos;
    }
}

if (!isset($GLOBALS['connCalendario']) || !($GLOBALS['connCalendario'] instanceof mysqli)) {
    $GLOBALS['connCalendario'] = conectarBanco();
}
?>
