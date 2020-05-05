<?php 
    include('config.php');
    $mes = (isset($_GET['mes'])) ? (int)$_GET['mes'] : date('m', time());
    $ano = (isset($_GET['ano'])) ? (int)$_GET['ano'] : date('Y', time());
    if($mes > 12) $mes = 12;
    else if($mes < 1) $mes = 1;

    $numeroDias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $diaInicialMes = date('N', strtotime("$ano-$mes-01")); //?Qual dia da semana comecou o mes;
    $diaInicialMes -= 1;
    if($diaInicialMes == -1) $diaInicialMes = 7;
    $meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outrubro', 'Novembro', 'Dezembro'];
    $hoje = [date('d', time()), date('m', time()), date('Y', time())];

    $tarefas = DataBase::query('SELECT * FROM `tb_tasks` WHERE `data` = ? ORDER BY `order_id`', ["$hoje[2]-$hoje[1]-$hoje[0]"]);
?>
<html lang="pt-br">
<head>
    <title>Web Agenda</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>css/jquery-ui.min.css">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>css/all.min.css">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>css/boot.css">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>css/style.css">
    <link rel="shortcut icon" href="<?php echo INCLUDE_PATH; ?>agenda.ico" type="image/x-icon">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo" style="background-image: url('agenda.png');"></div>
            <h1>Web Agenda</h1>
        </div><!--container-->
    </header>
    <section class="calendario">
        <div class="container">
            <h1 class="text-center titulo titulo2"><?php echo $meses[$mes-1] ?> de <?php echo $ano ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>Segunda</th>
                        <th>Terça</th>
                        <th>Quarta</th>
                        <th>Quinta</th>
                        <th>Sexta</th>
                        <th>Sábado</th>
                        <th>Domingo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $n = 1;
                        $z = 0;
                        $numeroDias += $diaInicialMes;
                        if($diaInicialMes == 7) {
                            $n = 8;
                            $z = 7;
                        }
                        while($n <= $numeroDias) {
                            if($n % 7 == 1) echo '<tr>';
                            if($z < $diaInicialMes) {
                                echo '<td>-</td>';
                                $z++;
                            } else {
                                $dia = $n - $diaInicialMes;
                                if($dia < 10) $dia = '0'.$dia;
                                if($dia == $hoje[0] and $mes == $hoje[1] and $ano == $hoje[2]) {
                                    $classeDia = getActivities("$ano-$mes-$dia");
                                    echo "<td class='today active $classeDia' data=\"$ano-$mes-$dia\">$dia</td>";
                                } else {
                                    $classeDia = getActivities("$ano-$mes-$dia");
                                    echo "<td class=\"$classeDia\" data=\"$ano-$mes-$dia\">$dia</td>";    
                                }
                            }
                            if($n % 7 == 0) echo '</tr>';
                            $n++;
                        }
                        $n--;
                        while($n % 7 != 0) {
                            echo '<td>-</td>';
                            $n++;
                        }
                        echo '</tr>';
                    ?>
                </tbody>
            </table>
            <div class="arrows-wrapper">
                <?php 
                    function getMes($atualMes, $atualAno, $req) {
                        $anoAnte = $atualAno;
                        $mesAnte = $atualMes + $req;
                        if($mesAnte <= 0) {
                            $mesAnte = 12;
                            $anoAnte -= 1;
                        } else if($mesAnte > 12) {
                            $mesAnte = 1;
                            $anoAnte += 1;
                        }
                        return [$mesAnte, $anoAnte];
                    }
                ?>
                
                <a class="arrow left" href="<?php INCLUDE_PATH; ?>?mes=<?php echo getMes($mes, $ano, -1)[0] ?>&ano=<?php echo getMes($mes, $ano, -1)[1] ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a class="arrow right" href="<?php INCLUDE_PATH; ?>?mes=<?php echo getMes($mes, $ano, +1)[0] ?>&ano=<?php echo getMes($mes, $ano, +1)[1] ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
                
                <div class="clear"></div>
            </div><!--arrows-wrapper-->
        </div><!--container-->
    </section><!--calendario-->

    <section class="activities">
        <div class="container">
            <div class="activities-wrapper">
                <div class="alert a-sucesso" style="display: none"><i class="fas fa-check"></i>Tarefa Adicionada Com Sucesso!</div>
                <h1 class="text-center titulo"><i class="fas fa-check-circle"></i> Tarefas para o dia <?php echo $hoje[0].'/'.$hoje[1].'/'.$hoje[2] ?></h1>
                <div class="add-button"><i class="fas fa-plus"></i></div>
                <div class="add-task">
                    <form method="POST">
                        <h1 class="text-center titulo"><i class="fas fa-check-circle"></i> Adicionar Tarefa para o dia <?php echo $hoje[0].'/'.$hoje[1].'/'.$hoje[2] ?></h1>
                        <input type="text" name="task" placeholder="Digite a Tarefa">
                        <input type="hidden" name="status" value="0">
                        <input type="hidden" name="data" value="<?php echo $hoje[2].'-'.$hoje[1].'-'.$hoje[0] ?>">
                        <input type="submit" value="Adicionar" name="acao">
                    </form>
                </div><!--add-task-->
                <div class="tarefas-wrapper">
                    <?php foreach($tarefas as $key => $value) {?>
                        <div class="tarefa-single <?php if($value['status'] == 1) echo 'done' ?>" id="item-<?php echo $value['id'] ?>">
                            <span><?php echo $value['nome'] ?></span>
                            <a>
                                <i idrm="<?php echo $value['id'] ?>" class="fas fa-times remove-task <?php if($value['status'] == 1) echo 'hide' ?>"></i>
                            </a>
                            <label class="checkbox-container">
                                <input type="checkbox" qual="<?php echo $value['id'] ?>" <?php if($value['status'] == 1) echo 'checked' ?>>
                                <span class="checkmark"><i class="fas fa-check"></i></span>
                            </label>
                        </div><!--tarefa-single-->
                    <?php } ?>
                </div><!--tarefas-wrapper-->
            </div><!--activities-wrapper-->
        </div><!--container-->
    </section><!--activities-->


    <script src="<?php echo INCLUDE_PATH; ?>js/jquery.js"></script>
    <script src="<?php echo INCLUDE_PATH; ?>js/jquery-ui.min.js"></script>
    <script src="<?php echo INCLUDE_PATH; ?>js/functions.js"></script>
</body>
</html>