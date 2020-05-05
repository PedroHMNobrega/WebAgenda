<?php 
    date_default_timezone_set('America/Sao_Paulo');
    define('INCLUDE_PATH', 'http://localhost/FullStack/Projetos/WebAgenda/');
    define('HOST', 'localhost');
    define('DB', 'web_agenda');
    define('USER', 'root');
    define('PASS', '');

    $autoload = function($class) {
        include('classes/'.$class.'.php');
    };
    spl_autoload_register($autoload);

    function alert($tipo, $msg) {
        if($tipo == 'sucesso') 
            echo '<div class="alert a-sucesso"><i class="fas fa-check"></i>'.$msg.'</div>';
        else if($tipo == 'erro')
            echo '<div class="alert a-erro"><i class="fas fa-times"></i>'.$msg.'</div>';
        else if($tipo == 'atencao')
            echo '<div class="alert a-atencao"><i class="fas fa-exclamation-triangle"></i>'.$msg.'</div>';
        echo "<script>setTimeout(function() { $('.alert').fadeOut(); }, 3000);</script>";
    }

    function redirect($url) {
        echo "<script>location.href='$url'</script>";
        die();
    }

    function getActivities($data) {
        $tarefas = DataBase::query('SELECT MIN(status) FROM `tb_tasks` WHERE `data` = ?', [$data], '');
        if($tarefas[0] == 1) return 'alldone';
        else if($tarefas[0] === "0") return 'has-activities';
        
    }
?>