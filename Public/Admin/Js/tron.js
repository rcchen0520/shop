$("tr.tron").mouseover(function () {
    $(this).find("td").css("backgroundColor","#BBDDE5");
});
$("tr.tron").mouseout(function () {
    $(this).find("td").css("backgroundColor","#FFF");
})