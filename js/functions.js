$(function() {
    var includePath = 'http://localhost/FullStack/Projetos/WebAgenda/'; 
    
    doneTask();
    selectDay();
    addTaskPage();
    sortTask();
    addTask();
    removeTask();

    function addTask() {
        $('.add-task form').submit(function() {
            $nome = $('.add-task input[name=task]').val();
            if($nome != '') {
                $status = '0';
                $data = $('.add-task input[name=data]').val();
                $.ajax({
                    url: includePath+'ajax/ajax.php',
                    method: 'post',
                    data: {'addTask': $nome, 'status': '0', 'data': $data}
                }).done(function(data) {
                    $('.tarefas-wrapper').append(data);
                    $('.add-task input[name=task]').val('');
                    
                    $('.activities-wrapper .alert').fadeIn();
                    setTimeout(function() { 
                        $('.activities-wrapper .alert').fadeOut();
                    }, 3000);
                });
            }
            return false;
        });
    }

    function removeTask() {
        $(document).on('click', '.remove-task', function() {
            let el = $(this);
            let id = el.attr('idrm');
            $.ajax({
                url: includePath+'ajax/ajax.php',
                method: 'post',
                data: {'removeTask': id}
            }).done(function(data) {
                if(data == 'true') {
                    el.parent().parent().fadeOut();
                    setTimeout(function() {el.parent().parent().remove();}, 1000);
                }
            });
        });
    }

    function doneTask() {
        $(document).on('change', 'input[type=checkbox]', function() {
            let removeTask = $('.remove-task');
            let father = $(this).parent().parent();
            let id = $(this).attr('qual');
            if(this.checked) {
                father.css('background-color', '#05974e');
                father.find(removeTask).addClass('hide');
                $.ajax({
                    url: includePath+'ajax/ajax.php',
                    method: 'post',
                    data: {'done': id}
                }).done(function(data) {
                    console.log(data);
                });
            } else {
                father.css('background-color', '#055497');
                father.find(removeTask).removeClass('hide');
                $.ajax({
                    url: includePath+'ajax/ajax.php',
                    method: 'post',
                    data: {'undone': id}
                }).done(function(data) {
                    console.log(data);
                });
            }
        });
    }

    function selectDay() {
        let day = $('td');
        day.click(function() {
            if($(this).html() != '-') {
                day.removeClass('active');
                $(this).addClass('active');
                let date = $(this).attr('data');
                trocarData(date);
                getTasks(date);
            }
        });
    }

    function trocarData(date2) {
        date = date2.split('-'); 
        date[1] = date[1].padStart(2, '0');
        // if(date[1] < 10) date[1] = '0'+date[1];
        $('.activities-wrapper h1').html('<i class="fas fa-check-circle"></i> Tarefas para o dia '+date[2]+'/'+date[1]+'/'+date[0]);
        $('.add-task h1').html('<i class="fas fa-check-circle"></i> Adicionar Tarefa para o dia '+date[2]+'/'+date[1]+'/'+date[0]);
        $('.add-task input[name=data]').val(date2);
    }

    function getTasks(data) {
        $.ajax({
            url: includePath+'ajax/ajax.php',
            method: 'post',
            data: {'getTask': data}
        }).done(function(data) {
            $('.tarefas-wrapper').html(data);
        });
    }

    function addTaskPage() {
        let button = $('.add-button');
        let addTask = $('.add-task');
        button.click(function() {
            if($(this).hasClass('active-btn')) {
                button.removeClass('active-btn');
                addTask.hide();
            } else {
                button.addClass('active-btn');
                addTask.show();
            }
        });

        $('.add-task form').submit(function() {
            if($('input[type=text]').val() == '') return false;
        });   
    }

    function sortTask() {
        $('.tarefas-wrapper').sortable({
            update: function() {
                var data = $(this).sortable('serialize');//?Pega a ordem de como ficou os elementos;
                data += '&sort';
                $.ajax({
                    url: includePath+'ajax/ajax.php',
                    method: 'post',
                    data: data
                }).done(function(data) {
                    console.log(data);
                });
            }
        });
    }
});