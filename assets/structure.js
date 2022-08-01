
$("#modal").click(()=>{
    $("#permitModal").modal("show")
})
$("form").change(()=>{
    $("#modal").prop("disabled", false)
})
$("#modal-close").click(()=>{
    $("#permitModal").modal("hide");

})
$("#submit-form").click(()=>{
    $("#permitModal").modal("hide");
})

