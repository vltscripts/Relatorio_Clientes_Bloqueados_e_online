<?php
include('addons.class.php');

session_name('mka');
session_start();

if (!isset($_SESSION['MKA_Logado'])) {
    exit('Acesso negado... <a href="/admin/">Fazer Login</a>');
}

// Assuming $Manifest is defined somewhere before this code
$manifestTitle = isset($Manifest->{'name'}) ? $Manifest->{'name'} : '';
$manifestVersion = isset($Manifest->{'version'}) ? $Manifest->{'version'} : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>MK - AUTH :: <?php echo htmlspecialchars($manifestTitle . " - V " . $manifestVersion); ?></title>

    <link href="../../estilos/mk-auth.css" rel="stylesheet" type="text/css" />
    <link href="../../estilos/font-awesome.css" rel="stylesheet" type="text/css" />

    <script src="../../scripts/jquery.js"></script>
    <script src="../../scripts/mk-auth.js"></script>

    <style type="text/css">
        /* Estilos CSS personalizados */
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 40px;
        }

        form,
        .table-container,
        .client-count-container {
            width: 100%;
            margin: 0 auto;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="submit"],
        .clear-button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .clear-button {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .clear-button:hover {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        h1 {
            color: #4caf50;
        }

        .client-count-container {
            text-align: center;
            margin-top: 10px;
        }

        .client-count {
            color: #4caf50;
            font-weight: bold;
        }

        .client-count.blue {
            color: #2196F3;
        }

        .nome_cliente a {
            color: blue;
            text-decoration: none;
            font-weight: bold;
        }

        .nome_cliente a:hover {
            text-decoration: underline;
        }

        .nome_cliente td {
            text-align: center;
        }

        .nome_cliente:nth-child(odd) {
            background-color: #FFFF99;
        }

        /* Estilo para ressaltar letras */
        .highlighted {
            color: #f44336; /* Cor vermelha */
            font-weight: bold;
        }
    </style>

    <script type="text/javascript">
        function clearSearch() {
            document.getElementById('search').value = '';
            document.forms['searchForm'].submit();
        }

        document.addEventListener("DOMContentLoaded", function () {
            var cells = document.querySelectorAll('.table-container tbody td.plan-name');
            cells.forEach(function (cell) {
                cell.addEventListener('click', function () {
                    var planName = this.innerText;
                    document.getElementById('search').value = planName;
                    document.title = 'Painel: ' + planName;
                    document.forms['searchForm'].submit();
                });
            });

            var calledStationIdCells = document.querySelectorAll('.table-container tbody td.calledstationid');
            calledStationIdCells.forEach(function (cell) {
                cell.addEventListener('click', function () {
                    var calledStationId = this.innerText;
                    document.getElementById('search').value = calledStationId;
                    document.forms['searchForm'].submit();
                });
            });
        });
    </script>

</head>

<body>
    <?php include('../../topo.php'); ?>

    <nav class="breadcrumb has-bullet-separator is-centered" aria-label="breadcrumbs">
        <ul>
            <li><a href="#"> ADDON</a></li>
            <li class="is-active">
                <a href="#" aria-current="page"> <?php echo htmlspecialchars($manifestTitle . " - V " . $manifestVersion); ?> </a>
            </li>
        </ul>
    </nav>

    <?php include('config.php'); ?>

    <?php
    if ($acesso_permitido) {
        // Formulário Atualizado com Funcionalidade de Busca
    ?>
<form id="searchForm" method="GET">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div style="width: 60%; margin-right: 10px;">
            <label for="search" style="font-weight: bold; margin-bottom: 5px;">Buscar Cliente:</label>
            <input type="text" id="search" name="search" placeholder="Digite o Login ou Usuário" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc;">
        </div>
        <div style="width: 20%; margin-right: 10px;">
            <label for="status" style="font-weight: bold; margin-bottom: 5px;">Status:</label>
            <select id="status" name="status" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc;">
                <option value="all" <?php echo (!empty($_GET['status']) && $_GET['status'] == 'all') ? 'selected' : ''; ?>>Todos</option>
                <option value="online" <?php echo (!empty($_GET['status']) && $_GET['status'] == 'online') ? 'selected' : ''; ?>>Online</option>
                <option value="offline" <?php echo (!empty($_GET['status']) && $_GET['status'] == 'offline') ? 'selected' : ''; ?>>Offline</option>
            </select>
        </div>
        <div style="flex: 1;">
            <input type="submit" value="Buscar" style="width: 100%; padding: 10px; border: 1px solid #4caf50; background-color: #4caf50; color: white; font-weight: bold; cursor: pointer; border-radius: 5px;">
        </div>
        <div style="flex: 1; margin-left: 10px;">
        <button type="button" onclick="clearSearch()" class="clear-button" style="width: 100%; padding: 10px; border: 1px solid #e74c3c; background-color: #e74c3c; color: white; font-weight: bold; cursor: pointer; border-radius: 5px;">Limpar</button>
        </div>
    </div>
</form>
<script>
function clearSearch() {
    // Limpa o campo de pesquisa
    document.getElementById('search').value = '';
    
    // Atualiza o valor do campo de seleção de status para "todos"
    document.getElementById('status').value = 'todos';
    
    // Submeta o formulário
    document.getElementById('searchForm').submit();
}
</script>


<?php
// Dados de conexão com o banco de dados já estão em config.php
// Inicialize a consulta SQL base
$countQuery = "SELECT COUNT(DISTINCT c.login) AS client_count 
                FROM sis_cliente c 
                LEFT JOIN radacct r ON c.login = r.username
                WHERE c.bloqueado = 'sim' 
                AND c.cli_ativado = 's' ";

// Verifique se uma pesquisa foi realizada
if (!empty($_GET['search'])) {
    $search = '%' . mysqli_real_escape_string($link, $_GET['search']) . '%';
    $countQuery .= " AND (c.login LIKE ? OR c.nome LIKE ? OR r.calledstationid LIKE ?)";
}

// Verifique se o status foi selecionado
if (!empty($_GET['status'])) {
    if ($_GET['status'] == 'online') {
        // Se o status for online, inclua apenas clientes com uma sessão ativa
        $countQuery .= " AND r.acctstoptime IS NULL 
                        AND r.radacctid IS NOT NULL";
    } elseif ($_GET['status'] == 'offline') {
        // Se o status for offline, inclua apenas clientes sem sessão ativa
        $countQuery .= " AND IFNULL(r.acctstoptime, '1970-01-01 00:00:00') < NOW() 
                        AND NOT EXISTS (
                            SELECT 1 FROM radacct ra WHERE ra.username = c.login AND ra.acctstoptime IS NULL
                        )";
    }
}

// Prepare a consulta SQL
$stmt = mysqli_prepare($link, $countQuery);

// Se uma pesquisa foi realizada, vincule os parâmetros de pesquisa
if (!empty($_GET['search'])) {
    mysqli_stmt_bind_param($stmt, "sss", $search, $search, $search);
}

// Execute a consulta SQL
mysqli_stmt_execute($stmt);
$countResult = mysqli_stmt_get_result($stmt);

if ($countResult) {
    $countRow = mysqli_fetch_assoc($countResult);
    $clientCount = $countRow['client_count'];

    echo "<div class='client-count-container'><p class='client-count blue'>Quantidade de clientes: $clientCount</p></div>";
} else {
    echo "<div class='client-count-container'><p class='client-count blue'>Erro ao obter a quantidade de clientes</p></div>";
}

// Tabela: Nomes dos Clientes com Logins Lado a Lado
?>

<div class="table-container">
 <table>
    <thead>
        <tr>
            <th style='text-align: center;'>Nome do Cliente</th>
            <th style='text-align: center;'>Login</th>
            <th style='text-align: center;'>Servidor</th>
            <th style='text-align: center;'>Boletos Vencidos</th>
            <th style='text-align: center;'>Tempo Offline</th> <!-- Nova coluna adicionada -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Adicione a condição de busca, se houver
        $searchCondition = '';
        if (!empty($_GET['search'])) {
            $search = mysqli_real_escape_string($link, $_GET['search']);
            $searchCondition = " AND (c.login LIKE '%$search%' OR c.nome LIKE '%$search%' OR r.calledstationid LIKE '%$search%')";
        }

        // Consulta SQL para obter os clientes com base no status selecionado e na busca
        $query = "SELECT c.uuid_cliente, c.nome, c.login, MAX(r.calledstationid) AS calledstationid, c.tit_vencidos,
                         MAX(r.acctstarttime) AS ultima_conexao
                  FROM sis_cliente c
                  LEFT JOIN radacct r ON c.login = r.username
                  WHERE c.bloqueado = 'sim' 
                  AND c.cli_ativado = 's'";

        // Adicione a condição de busca à consulta principal
        $query .= $searchCondition;

        // Verifica se a opção "status" foi enviada via GET e ajusta a consulta SQL conforme necessário
        if (!empty($_GET['status'])) {
            if ($_GET['status'] == 'online') {
                // Se o status for online, seleciona apenas os clientes com uma sessão ativa
                $query .= " AND r.acctstoptime IS NULL 
                            AND r.radacctid IS NOT NULL";
            } elseif ($_GET['status'] == 'offline') {
                // Se o status for offline, seleciona apenas os clientes sem sessão ativa
                $query .= " AND IFNULL(r.acctstoptime, '1970-01-01 00:00:00') < NOW() 
                            AND NOT EXISTS (
                                SELECT 1 FROM radacct ra WHERE ra.username = c.login AND ra.acctstoptime IS NULL
                            )";
            }
        }

        $query .= " GROUP BY c.uuid_cliente, c.nome, c.login, c.tit_vencidos
                    ORDER BY c.nome ASC";

        // Execute a consulta
        $result = mysqli_query($link, $query);

        // Verifique se a consulta foi bem-sucedida
        if ($result) {
// Exiba os resultados da consulta SQL
while ($row = mysqli_fetch_assoc($result)) {
    $nome_por_num_titulo = "Nome do Cliente: " . $row['nome'] . " - UUID: " . $row['uuid_cliente'];

    // Adiciona a classe 'nome_cliente' e 'highlight' (para linhas ímpares) alternadamente
    $nomeClienteClass = ($rowNumber % 2 == 0) ? 'nome_cliente' : 'nome_cliente highlight';

    // Adiciona o link apenas no campo de nome do cliente
    echo "<tr class='$nomeClienteClass'>";
    echo "<td style='border: 1px solid #ddd; padding: 2px;'><a href='../../cliente_det.hhvm?uuid=" . $row['uuid_cliente'] . "' target='_blank' >" . $row['nome'] . "</a></td>";
    echo "<td style='border: 1px solid #ddd; padding: 2px;'><a href='../../relatorios_u.hhvm?login=" . $row['login'] . "' target='_blank' >" . $row['login'] . "</a></td>";
    echo "<td style='border: 1px solid #ddd; padding: 2px; text-align: center; font-weight: bold;' class='calledstationid'><a target='_blank' style='color: #06683e;'>" . $row['calledstationid'] . "</a></td>";
    echo "<td style='border: 1px solid #ddd; padding: 2px; text-align: center; color: #f44336; font-weight: bold;' class='highlighted'>" . $row['tit_vencidos'] . "</td>";
    
    // Verifica se o cliente está online e exibe "Ativo" em vez da última conexão
    if (!empty($row['ultima_conexao']) && $_GET['status'] == 'online') {
        echo "<td style='border: 1px solid #ddd; padding: 4px; text-align: center; color: #0d6cea; font-weight: bold;'>Ativo</td>";
    } else {
        // Calcula o tempo offline em segundos
        $ultimaConexao = strtotime($row['ultima_conexao']);
        $tempoOffline = time() - $ultimaConexao;

        // Converte o tempo offline para dias, horas, minutos e segundos
        $dias = floor($tempoOffline / (60 * 60 * 24));
        $horas = floor(($tempoOffline % (60 * 60 * 24)) / (60 * 60));
        $minutos = floor(($tempoOffline % (60 * 60)) / 60);
        $segundos = $tempoOffline % 60;

        // Exibe o tempo offline formatado
       echo "<td style='border: 1px solid #ddd; padding: 4px; text-align: center; color: #0d6cea; font-weight: bold;' class='highlighted'>$dias dias, $horas horas, $minutos minutos </td>";
    }
    
    echo "</tr>";
}
        } else {
            // Se a consulta falhar, exiba uma mensagem de erro
            echo "<tr><td colspan='5'>Erro na consulta: " . mysqli_error($link) . "</td></tr>";
        }
        ?>
    </tbody>
</table>

        </div>
    <?php
    } else {
        echo "Acesso não permitido!";
    }
    ?>

    <?php include('../../baixo.php'); ?>

    <script src="../../menu.js.php"></script>
    <?php include('../../rodape.php'); ?>
</body>

</html>
