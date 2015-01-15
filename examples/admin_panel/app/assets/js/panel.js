function show_modal_(title, body, buttons) {
    $("#_show_modal .modal-title").text( title );
    $("#_show_modal .modal-body").text( body );
    $("#_show_modal .modal-footer").html("");
    for (var i=0; i < buttons.length ;  i++) {
        var btn = $('<button data-toggle="button" type="button" class="btn btn-default">'+buttons[i].text+'</button>');
        $(btn).bind('click', buttons[i], function(evt) {

            this.close_modal = function() {
                $("#_show_modal").modal('hide');
            }

            evt.data.handler(this);
        });
        $("#_show_modal .modal-footer").append( btn );
    };

    $("#_show_modal").modal('show');
}