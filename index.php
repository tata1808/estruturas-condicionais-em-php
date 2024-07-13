<?php
session_start();

class Aluno {
    public $nome;
    public $notas = [];
    public $media = 0;
    public $total = 0;
    public $resultado = "";

    public function __construct($nome) {
        $this->nome = $nome;
    }

    public function atribuirNotas($notas) {
        $this->notas = $notas;
        $this->calcularMedia();
    }

    private function calcularMedia() {
        $this->total = array_sum($this->notas);
        $this->media = $this->total / count($this->notas);
        $this->determinarResultado();
    }

    private function determinarResultado() {
        if ($this->media < 4) {
            $this->resultado = "Reprovado";
        } elseif ($this->media >= 4 && $this->media <= 6) {
            $this->resultado = "Recuperação";
        } else {
            $this->resultado = "Aprovado";
        }
    }

    public function editarResultado($novoResultado) {
        $this->resultado = $novoResultado;
    }
}

if (!isset($_SESSION['alunos'])) {
    $_SESSION['alunos'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'cadastrar') {
        if (count($_SESSION['alunos']) < 5) {
            $nome = $_POST['nome'];
            $aluno = new Aluno($nome);
            $_SESSION['alunos'][] = $aluno;
        } else {
            echo "Máximo de 5 alunos já cadastrados.";
        }
    } elseif ($action == 'atribuir') {
        $index = $_POST['index'];
        $notas = array_map('floatval', $_POST['notas']);
        if (in_array($index, array_keys($_SESSION['alunos']))) {
            $_SESSION['alunos'][$index]->atribuirNotas($notas);
        }
    } elseif ($action == 'editar') {
        $index = $_POST['index'];
        $novoResultado = $_POST['resultado'];
        if (in_array($index, array_keys($_SESSION['alunos']))) {
            $_SESSION['alunos'][$index]->editarResultado($novoResultado);
        }
    }
}

function renderAlunos() {
    foreach ($_SESSION['alunos'] as $index => $aluno) {
        echo "<tr>";
        echo "<td>{$aluno->nome}</td>";
        echo "<td>" . implode(', ', $aluno->notas) . "</td>";
        echo "<td>{$aluno->total}</td>";
        echo "<td>{$aluno->media}</td>";
        echo "<td>{$aluno->resultado}</td>";
        echo "<td><form method='POST'><input type='hidden' name='action' value='editar'><input type='hidden' name='index' value='{$index}'><input type='text' name='resultado' value='{$aluno->resultado}'><button type='submit'>Editar</button></form></td>";
        echo "</tr>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestão de Notas</title>
</head>
<body>
    <h1>Sistema de Gestão de Notas</h1>

    <h2>Cadastrar Aluno</h2>
    <form method="POST">
        <input type="hidden" name="action" value="cadastrar">
        <input type="text" name="nome" placeholder="Nome do aluno" required>
        <button type="submit">Cadastrar</button>
    </form>

    <h2>Atribuir Notas</h2>
    <form method="POST">
        <input type="hidden" name="action" value="atribuir">
        <select name="index">
            <?php foreach ($_SESSION['alunos'] as $index => $aluno) : ?>
                <option value="<?= $index ?>"><?= $aluno->nome ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="notas[]" min="0" max="10" step="0.1" required>
        <input type="number" name="notas[]" min="0" max="10" step="0.1" required>
        <input type="number" name="notas[]" min="0" max="10" step="0.1" required>
        <input type="number" name="notas[]" min="0" max="10" step="0.1" required>
        <button type="submit">Atribuir Notas</button>
    </form>

    <h2>Resultados dos Alunos</h2>
    <table border="1">
        <tr>
            <th>Nome</th>
            <th>Notas</th>
            <th>Total</th>
            <th>Média</th>
            <th>Resultado</th>
            <th>Editar Resultado</th>
        </tr>
        <?php renderAlunos(); ?>
    </table>
</body>
</html>
