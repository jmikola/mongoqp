$("a[rel=tooltip]").tooltip();

$("form.collection-selector").submit(function(e){
    e.preventDefault();
    var url = window.location.protocol + "//" + window.location.hostname;

    if (!(80 == window.location.port && "http:" == window.location.protocol) &&
        !(443 == window.location.port && "https:" == window.location.protocol)) {
        url += ":" + window.location.port;
    }

    url += window.location.pathname + "/" + $("input", this).val();

    window.location.assign(url);
});

$("form.profiling-control button").click(function(e) {
    var action = $(this).closest('form').attr('action');

    var level = $(this).data('level');

    $.post(action, { level: level });
});

$.extend( $.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
});

// See: http://www.datatables.net/plug-ins/sorting
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "numeric-comma-html-pre": function ( a ) {
        var x = a.replace( "\n", "" );
        x = x.replace( /<.*?>/g, "" );
        x = $.trim(x);
        x = (x == "-") ? 0 : x.replace( /,/, "" );
        return parseFloat( x );
    },

    "numeric-comma-html-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "numeric-comma-html-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );

// See: http://datatables.net/blog/Twitter_Bootstrap
$("table.profiles").has("tbody tr td:eq(2)").dataTable({
    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
    //"sDom": "<'pull-left'l><'pull-right'f><'clearfix'>t<'pull-left'i><'pull-right'p><'clearfix'>",
    "sPaginationType": "bootstrap",
    "aoColumns": [
        null,
        null,
        { "sType": "numeric-comma-html" },
        { "sType": "numeric-comma-html" },
        { "sType": "numeric-comma-html" },
        { "sType": "numeric-comma-html" },
        null
    ]
});
