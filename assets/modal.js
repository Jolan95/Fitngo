
// modal handle permit
$("#modal").click(()=>{
    $("#permitModal").modal("show")
})
$("form#form_edit_permit").change(()=>{
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

$("#close-flash").on("click", ()=> {
    $(".alert").alert('close')
})


//onglets Mails/Contents
$("#mail-button").click(()=>{
    $("#mail-main").attr("hidden",false);
    $("#page-main").attr("hidden",true);
    $("#mail-secondary").attr("hidden",true);
})
$("#mailSecond-button").click(()=>{
    $("#mail-secondary").attr("hidden",false);
    $("#page-main").attr("hidden",true);
    $("#mail-main").attr("hidden",true);
})
$("#page-button").click(()=>{
    $("#page-main").attr("hidden",false);
    $("#mail-secondary").attr("hidden",true);
    $("#mail-main").attr("hidden",true);
})

