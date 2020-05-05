<?php 
    include('../config.php');
    if(isset($_POST['done'])) {
        DataBase::editar(['status'=> '1'], $_POST['done'], 'tb_tasks');
        die();
    } else if(isset($_POST['undone'])) {
        DataBase::editar(['status'=> '0'], $_POST['undone'], 'tb_tasks');
        die();
    } else if(isset($_POST['sort'])) {
        foreach($_POST['item'] as $key => $value) {
            DataBase::editar(['order_id'=> $key+1], $value, 'tb_tasks');
        }
        die();
    } else if(isset($_POST['getTask'])) {
        $tarefas = DataBase::query('SELECT * FROM `tb_tasks` WHERE `data` = ? ORDER BY `order_id`', [$_POST['getTask']]);
        foreach($tarefas as $key => $value) {
            $v1 = ($value['status'] == 1) ? 'done' : '';
            $v2 = ($value['status'] == 1) ? 'hide' : '';
            $v3 = ($value['status'] == 1) ? 'checked' : '';
            $INCP = INCLUDE_PATH;
            print <<<HTML
                <div class="tarefa-single $v1" id="item-$value[id]">
                    <span>$value[nome]</span>
                    <a>
                        <i idrm="$value[id]" class="fas fa-times remove-task $v2"></i>
                    </a>
                    <label class="checkbox-container">
                        <input type="checkbox" qual="$value[id]" $v3>
                        <span class="checkmark"><i class="fas fa-check"></i></span>
                    </label>
                </div><!--tarefa-single-->
            HTML;
        } 
        die();
    } else if(isset($_POST['addTask'])) {
        if(DataBase::adicionar($_POST, 'tb_tasks')) {
            $tarefa = DataBase::query('SELECT * FROM `tb_tasks` ORDER BY id DESC LIMIT 1', '')[0];
            $INCP = INCLUDE_PATH;$INCP = INCLUDE_PATH;
            print <<<HTML
                <div class="tarefa-single" id="item-$tarefa[id]">
                    <span>$tarefa[nome]</span>
                    <a>
                        <i idrm="$tarefa[id]" class="fas fa-times remove-task"></i>
                    </a>
                    <label class="checkbox-container">
                        <input type="checkbox" qual="$tarefa[id]">
                        <span class="checkmark"><i class="fas fa-check"></i></span>
                    </label>
                </div>
            HTML;
        } 
    } else if(isset($_POST['removeTask'])) {
        if(DataBase::remover($_POST['removeTask'], 'tb_tasks')) die('true');
    }
?>