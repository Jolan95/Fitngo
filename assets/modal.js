
// modal handle permit
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

// modal remove franchise
$("#remove-franchise").click(()=>{
    $("#remove-franchise-modal").modal("show")  
})
$("#modal-remove-close").click(()=>{
    $("#remove-franchise-modal").modal("hide");
})

// modal remove structure
$("#remove-structure").click(()=>{
    $("#remove-structure-modal").modal("show")  
})
$("#remove-structure-close").click(()=>{
    $("#remove-structure-modal").modal("hide");
})

// modal logout structure
$("#logout").click(()=>{
    $("#logout-modal").modal("show")  
})
$("#modal-logout-close").click(()=>{
    $("#logout-modal").modal("hide");
})

