$(function () {
    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");
});