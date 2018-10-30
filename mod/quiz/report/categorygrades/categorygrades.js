
function quiz_categorygrades_printurl(url, callback) {
    var frame1 = document.createElement('iframe');
    frame1.name = "frame1";
    frame1.style.position = "absolute";
    frame1.style.top = "-1000000px";
    frame1.onload = function(elem) {
        window.frames["frame1"].focus();
        window.frames["frame1"].print();
        callback();
    }
    frame1.src = url;
    document.body.appendChild(frame1);
    return false;
}

function quiz_categorygrades_printone(e, args) {
    e.preventDefault();
    Y.log(args.qubaid);
    spinner = document.createElement('i');
    spinner.setAttribute('class', 'fa fa-spinner fa-pulse');
    var printlink = document.getElementById('categorygrades_print_single_' + args.qubaid);
    printlink.appendChild(spinner);
    var url = '/mod/quiz/report.php?mode=categorygrades&id=' + args.cmid + '&qubaid=' + args.qubaid;
    quiz_categorygrades_printurl(url, function() {printlink.removeChild(spinner)});
}

