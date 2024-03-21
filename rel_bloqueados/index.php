<?php
// INCLUE FUNCOES DE ADDONS -----------------------------------------------------------------------
include('addons.class.php');

// VERIFICA SE O USUARIO ESTA LOGADO --------------------------------------------------------------
session_name('mka');
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['MKA_Logado'])) exit('Acesso negado... <a href="/admin/">Fazer Login</a>');
// VERIFICA SE O USUARIO ESTA LOGADO --------------------------------------------------------------

// Assuming $Manifest is defined somewhere before this code
$manifestTitle = isset($Manifest->{'name'}) ? $Manifest->{'name'} : '';
$manifestVersion = isset($Manifest->{'version'}) ? $Manifest->{'version'} : '';
?>

<!DOCTYPE html>
<?php
if (isset($_SESSION['MM_Usuario'])) {
    echo '<html lang="pt-BR">'; // Fix versão antiga MK-AUTH
} else {
    echo '<html lang="pt-BR" class="has-navbar-fixed-top">';
}
?>
<html lang="pt-BR">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>MK - AUTH :: <?php echo $Manifest->{'name'} . " - V " . $Manifest->{'version'};  ?></title>

    <link href="../../estilos/mk-auth.css" rel="stylesheet" type="text/css" />
    <link href="../../estilos/font-awesome.css" rel="stylesheet" type="text/css" />

    <script src="../../scripts/jquery.js"></script>
    <script src="../../scripts/mk-auth.js"></script>
    <link href="../../estilos/bi-icons.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/css.css" rel="stylesheet" type="text/css" />

    <style type="text/css">
        /* Estilos CSS personalizados */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #333;

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
        /* Estilo para layout inteiro */
        table th,
        table td {
            padding: 5px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #0d6cea;
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
		/* Estilo do botao ordenar */
		    .sort-button {
        padding: 1px 10px;
        background-color: #1e7bf3;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        }

        .sort-button:hover {
        background-color: #45a049;
        }
		/* Estilos Nome do Cliente e Login */
       .red-text {
        color: #f44336; /* Cor vermelha */
       }
	   /* Estilos CSS personalizados */
       .nome_cliente td {
       text-align: center;
       max-width: 260px; /* Defina a largura máxima desejada */
       overflow: hidden;
       white-space: nowrap;
       text-overflow: ellipsis; /* Adiciona reticências (...) para indicar que o texto foi cortado */
       }

    </style>

<script>
    var sortDirection = 'asc'; // Definir a direção inicial da ordenação como ascendente

    function sortTable(columnIndex) {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.querySelector('.table-container table');
        switching = true;
        
        // Loop até que nenhuma troca precise ser feita
        while (switching) {
            switching = false;
            rows = table.rows;
            
            // Loop através de todas as linhas, exceto o cabeçalho
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                
                // Obtenha os elementos a serem comparados, com base na coluna especificada
                x = rows[i].getElementsByTagName('td')[columnIndex];
                y = rows[i + 1].getElementsByTagName('td')[columnIndex];
                
                // Verifique se a direção atual da ordenação é ascendente
                if (sortDirection === 'asc') {
                    // Compare os elementos, alterando shouldSwitch se necessário
                    if (parseInt(x.getAttribute('data-seconds')) > parseInt(y.getAttribute('data-seconds'))) {
                        shouldSwitch = true;
                        break;
                    }
                } else {
                    // Se a direção for descendente, faça a comparação inversa
                    if (parseInt(x.getAttribute('data-seconds')) < parseInt(y.getAttribute('data-seconds'))) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            
            if (shouldSwitch) {
                // Se shouldSwitch for verdadeiro, troque as posições e marque switching como verdadeiro para outro loop
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
        
        // Alterne a direção da ordenação para o próximo clique
        sortDirection = (sortDirection === 'asc') ? 'desc' : 'asc';
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
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 10px;">
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
        <div style="display: flex; align-items: flex-end;">
            <input type="submit" value="Buscar" style="padding: 10px; border: 1px solid #4caf50; background-color: #4caf50; color: white; font-weight: bold; cursor: pointer; border-radius: 5px; margin-right: 10px;">
            <button type="button" onclick="clearSearch()" class="clear-button" style="padding: 10px; border: 1px solid #e74c3c; background-color: #e74c3c; color: white; font-weight: bold; cursor: pointer; border-radius: 5px; margin-right: 10px;">Limpar</button>
            <button type="button" onclick="sortTable2(4)" class="clear-button sort-button-2" style="padding: 10px; border: 1px solid #4336f4; background-color: #4336f4; color: white; font-weight: bold; cursor: pointer; border-radius: 5px;">Ordenar</button>
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

<script>
    var sortDirection2 = 'desc'; // Definir a direção inicial da ordenação como descendente
    var sortColumnIndex2 = 4; // Índice da coluna de data de bloqueio

    // Função para ordenar a tabela
    function sortTable2() {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.querySelector('.table-container table');
        switching = true;
        
        while (switching) {
            switching = false;
            rows = table.rows;
            
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = getDateFromString(rows[i].getElementsByTagName('td')[sortColumnIndex2].textContent);
                y = getDateFromString(rows[i + 1].getElementsByTagName('td')[sortColumnIndex2].textContent);
                
                if (sortDirection2 === 'desc') {
                    if (x < y) {
                        shouldSwitch = true;
                        break;
                    }
                } else {
                    if (x > y) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }

    // Função auxiliar para converter string de data em objeto Date
    function getDateFromString(dateString) {
        var parts = dateString.split('-');
        var day = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10) - 1; // Mês é baseado em zero (janeiro é 0)
        var year = parseInt(parts[2], 10);
        return new Date(year, month, day);
    }

    // Adicionando evento de clique ao botão de ordenação
    document.addEventListener("DOMContentLoaded", function() {
        var sortButton2 = document.querySelector('.sort-button-2');
        sortButton2.addEventListener('click', function(event) {
            event.preventDefault();
            sortTable2();
            // Alternar a direção da ordenação após cada clique
            sortDirection2 = (sortDirection2 === 'desc') ? 'asc' : 'desc';
        });
    });
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
                        <th style='text-align: center;'>Data de Bloqueio</th>
                        <th style='text-align: center;'>Tempo Offline <span id="sortButtonContainer"><?php if ($_GET['status'] === 'offline') { echo "<button class=\"sort-button\" onclick=\"sortTable(5)\">Ordenar</button>"; } ?></span></th> <!-- Nova coluna adicionada -->
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
// Modifique a consulta SQL para incluir a coluna data_bloq
$query = "SELECT c.uuid_cliente, c.nome, c.login, MAX(r.calledstationid) AS calledstationid, c.tit_vencidos,
                MAX(r.acctstarttime) AS ultima_conexao,
                MAX(r.acctstoptime) AS ultima_desconexao,
                IFNULL((
                    SELECT IF(r.acctstoptime IS NULL AND r.radacctid IS NOT NULL, 'online', 'offline') 
                    FROM radacct r 
                    WHERE r.username = c.login 
                    ORDER BY r.acctstarttime DESC 
                    LIMIT 1
                ), 'offline') AS status,
                c.data_bloq
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

$query .= " GROUP BY c.uuid_cliente, c.nome, c.login, c.tit_vencidos, c.data_bloq
            ORDER BY c.nome ASC";

// Execute a consulta
$result = mysqli_query($link, $query);

// Execute a consulta
$result = mysqli_query($link, $query);

// Verifique se a consulta foi bem-sucedida
if ($result) {
	
// Exiba os resultados da consulta SQL
while ($row = mysqli_fetch_assoc($result)) {
    $nome_por_num_titulo = "Nome do Cliente: " . $row['nome'] . " - UUID: " . $row['uuid_cliente'];

    // Adiciona a classe 'nome_cliente' e 'highlight' (para linhas ímpares) alternadamente
    $nomeClienteClass = ($rowNumber % 2 == 0) ? 'nome_cliente' : 'nome_cliente highlight';

    // Converta a data para um formato de timestamp usando strtotime()
    $dataBloqTimestamp = strtotime($row['data_bloq']);

    // Formate a data para o formato desejado
    $dataBloqFormatada = date('d-m-Y', $dataBloqTimestamp);

// Nome do Cliente	
echo "<tr class='$nomeClienteClass'>";
echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; font-weight: bold; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 150px;'>"; // Definindo a largura da célula para 150px
echo "<a href='../../cliente_alt.hhvm?uuid=" . $row['uuid_cliente'] . "' target='_blank' style='color: #06683e; display: flex; align-items: center;' title='" . $row['nome'] . "'>"; // Adicionando estilo inline e tooltip
echo "<img src='img/icon_cliente.png' alt='Cliente' style='margin-right: 5px; width: 25px; height: 25px;'>"; // Ajuste o tamanho conforme necessário
echo "<span class='red-text'>" . $row['nome'] . "</span>";
echo "</a>";
echo "</td>";

// Login
echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center;' title='Login: " . $row['login'] . "'>"; // Adicionando tooltip ao <td>
echo "<a href='../../relatorios_u.hhvm?login=" . $row['login'] . "' target='_blank' style='display: flex; align-items: center;'>";
echo "<img src='img/icon_globo.png' alt='Ícone Globo' style='width: 25px; height: 25px;'>";
echo "<span class='red-text' style='margin: auto;'>";
echo $row['login'];
echo "</span>";
echo "</a>";
echo "</td>";

// Servidor
echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; font-weight: bold; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;' class='calledstationid'>";
echo "<a target='_blank' style='color: #06683e; display: flex; align-items: center;' title='" . $row['calledstationid'] . "'>"; // Adicionando estilo inline e tooltip
echo "<img src='img/icon_servidor.png' alt='Servidor' style='margin-right: 5px; width: 25px; height: 25px;'>"; // Ajuste o tamanho conforme necessário
echo $row['calledstationid'];
echo "</a>";
echo "</td>";

// Boletos Vencidos
echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; font-weight: bold;' class='highlighted'>";
echo "<a href='../../cliente_det.hhvm?uuid=" . $row['uuid_cliente'] . "' target='_blank'>";
echo "<img src='img/icon_boleto.png' alt='Boletos Vencidos' style='vertical-align: middle; margin-right: 2px; width: 25px; height: 25px;'>";
echo $row['tit_vencidos'];
echo "</a>";
echo "</td>";

// Data de Bloqueio	Ativo
$status = $row['status'];
if ($status == 'online') {
	// Exiba a data formatada na tabela ativa
    echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; color: #078910; font-weight: bold;'>" . date('d-m-Y / H:i', strtotime($row['data_bloq'])) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold;'>";

    // Verifica se o status é "Ativo"
    if ($status == 'online') {
        // Se o cliente estiver ativo, exibe o ícone "Cliente Ativo" à esquerda do texto "Ativo"
        echo "<img src='img/icon_ativo.png' alt='Cliente Ativo' style='float: left; margin-right: 5px; width: 20px;'>"; // Ajuste o tamanho conforme necessário
        echo "<span style='color: #078910;'>Ativo</span>";
    } else {
        // Se o cliente estiver inativo, exibe apenas o texto "Inativo"
        echo "Inativo";
    }

// Tempo OFFLINE
echo "</td>";
} else {
    // Calcula o tempo offline em segundos
    $ultimaConexao = strtotime($row['ultima_desconexao']);
    $tempoOffline = time() - $ultimaConexao;

    // Calcula o tempo offline em dias, horas e minutos
    $days = floor($tempoOffline / (60 * 60 * 24));
    $remainingSeconds = $tempoOffline % (60 * 60 * 24);
    $hours = floor($remainingSeconds / (60 * 60));
    $remainingSeconds %= (60 * 60);
    $minutes = floor($remainingSeconds / 60);

    // Formata o tempo offline
    $offlineTimeFormatted = "";
    if ($days > 0) {
        $offlineTimeFormatted .= $days . "D, ";
    }
    $offlineTimeFormatted .= sprintf("%02d:%02d", $hours, $minutes);

    // Exiba a data formatada na tabela Off-line
    echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; color: #f44336; font-weight: bold;'>" . date('d-m-Y / H:i', strtotime($row['data_bloq'])) . "</td>";

    // Exibe o tempo offline formatado
    echo "<td style='border: 1px solid #ddd; padding: 4px; text-align: center; color: #000000; font-weight: bold;' class='highlighted' data-seconds='$tempoOffline'>";
    if ($status == 'offline') {
        echo "<img src='img/icon_bloqueado.png' alt='Cliente Bloqueado' style='float: left; margin-right: 5px; width: 20px;'>"; // Ajuste o tamanho conforme necessário
    }
    echo "$offlineTimeFormatted";
    echo "</td>";
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
