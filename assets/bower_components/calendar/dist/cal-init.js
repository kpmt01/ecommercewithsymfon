$(function() {

    // page is now ready, initialize the calendar...

    $('#calendar').fullCalendar({
        lang: 'es',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        events: {
            url: 'api/getAllReserve',
            data: function() {
                return {
                    dynamic_value: Math.random()
                };
            }
        },

        /*eventClick: function(calEvent, jsEvent, view) {
            console.log(calEvent, jsEvent, view);
            $.ajax({
                method: 'POST',
                url: 'api/getOneReserve',
                data: {date: calEvent.start._i,name: calEvent.title},
                success: function(res){
                    var temp = '<ul class="common-list">';
                    $.each(res,function(x, y){
                        temp += '<li><a href="javascript:void(0)"><b>' + x + '</b> ' + y + '</a></li>';
                    });
                    temp += '</ul>';
                    $('#clickDetail .modal-title').html(res.davet_sahibi_adi);
                    $('#clickDetail .modal-body').html(temp);
                    $('#clickDetail').modal('show');
                }
            });
        }*/

    })

});