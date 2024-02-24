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
            width: 80%;
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
            <label for="search">Buscar Cliente:</label>
            <input type="text" id="search" name="search" placeholder="Digite o nome do cliente ou Servidor" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <input type="submit" value="Buscar">
            <button type="button" onclick="clearSearch()" class="clear-button">Limpar</button>
        </form>

        <?php
        // Dados de conexão com o banco de dados já estão em config.php
        // Consulta SQL para obter a quantidade de clientes bloqueados e online
        $countQuery = "SELECT COUNT(c.login) AS client_count FROM sis_cliente c 
                    LEFT JOIN radacct r ON c.login = r.username
                    WHERE c.bloqueado = 'sim' 
                    AND c.cli_ativado = 's' 
                    AND r.acctstoptime IS NULL 
                    AND r.radacctid IS NOT NULL";

        if (!empty($_GET['search'])) {
            $countQuery .= " AND (c.login LIKE ? OR c.nome LIKE ? OR r.calledstationid LIKE ?)";
        }

        $stmt = mysqli_prepare($link, $countQuery);

        if (!empty($_GET['search'])) {
            $search = '%' . mysqli_real_escape_string($link, $_GET['search']) . '%';
            mysqli_stmt_bind_param($stmt, "sss", $search, $search, $search);
        }

        mysqli_stmt_execute($stmt);
        $countResult = mysqli_stmt_get_result($stmt);

        if ($countResult) {
            $countRow = mysqli_fetch_assoc($countResult);
            $clientCount = $countRow['client_count'];

            echo "<div class='client-count-container'><p class='client-count blue'>Quantidade de clientes bloqueados e online: $clientCount</p></div>";
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

                    // Consulta SQL para obter os clientes bloqueados e online
                    $query = "SELECT c.uuid_cliente, c.nome, c.login, MAX(r.calledstationid) AS calledstationid, c.tit_vencidos
                              FROM sis_cliente c
                              LEFT JOIN radacct r ON c.login = r.username
                              WHERE c.bloqueado = 'sim' 
                              AND c.cli_ativado = 's' 
                              AND r.acctstoptime IS NULL 
                              AND r.radacctid IS NOT NULL"
                        . $searchCondition .
                        " GROUP BY c.uuid_cliente, c.nome, c.login, c.tit_vencidos
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
                            echo "<td style='border: 1px solid #ddd; padding: 4px;'><a href='../../cliente_det.hhvm?uuid=" . $row['uuid_cliente'] . "' target='_blank' >" . $row['nome'] . "</a></td>";
                            echo "<td style='border: 1px solid #ddd; padding: 4px;'><a href='../../relatorios_u.hhvm?login=" . $row['login'] . "' target='_blank' >" . $row['login'] . "</a></td>";
                            echo "<td style='border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold;' class='calledstationid'><a target='_blank' style='color: #06683e;'>" . $row['calledstationid'] . "</a></td>";
                            echo "<td style='border: 1px solid #ddd; padding: 4px; text-align: center; color: #f44336; font-weight: bold;' class='highlighted'>" . $row['tit_vencidos'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        // Se a consulta falhar, exiba uma mensagem de erro
                        echo "<tr><td colspan='4'>Erro na consulta: " . mysqli_error($link) . "</td></tr>";
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
