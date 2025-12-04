$(document).ready(function() {

    $(document).on("click", ".delete-btn", function() {
        const button = $(this);
        const propertyId = button.data("id");

        if(!confirm("Are you sure you want to delete this property?")) return;

        $.ajax({
            url: `/properties/${propertyId}`,
            type: "DELETE",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if(res.success) {
                    button.closest("tr").remove();
                    window.pmShowToast("Property deleted successfully!", "success");
                }
            },
            error: function(xhr){
                window.pmShowToast("Error deleting property: " + (xhr.responseJSON?.message || xhr.statusText), "error");
            }
        });
    });

});
