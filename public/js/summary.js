$(document).ready(function () {

    var uri = location.href;
    uri = uri.split("/");
    if(uri[uri.length-1] == 'summary')
    {
        setInterval(function () {
           location.reload();
        },100000);
    }
});
