$(document).ready(function () {

    // ------------- Open Edit Modal -------------
    $(document).on("click", ".edit-btn", function () {
        const row = $(this).closest("tr");

        const property = {
            id: row.find("td:first-child").text().trim(),
            category: row.find("td:nth-child(2)").text().trim(),
            location: row.find("td:nth-child(3)").text().trim(),
            price: row.find("td:nth-child(4)").text().replace(" EGP","").replace(/,/g,"").trim(),
        status: row.find("td:nth-child(5)").text().trim(),
            user_id: row.find("td:nth-child(6)").text().trim() || undefined,
            description: row.data("description") || "",
            installment_years: row.data("installment") || undefined,
            transaction_type: row.data("transaction") || "sale",
        };

        $("#edit-property-id").val(property.id);
        $("#edit-category").val(property.category);
        $("#edit-location").val(property.location);
        $("#edit-price").val(property.price);
        $("#edit-status").val(property.status);
        $("#edit-user-id").val(property.user_id);
        $("#edit-description").val(property.description);
        $("#edit-installment-years").val(property.installment_years);
        $("#edit-transaction-type").val(property.transaction_type);

        $("#edit-property-container").fadeIn(200);
    });

    // ------------- Close Modal -------------
    function closeModal() {
        $("#edit-property-container").fadeOut(200);
        const form = $("#edit-property-form")[0];
        if(form) form.reset();
        $("#edit-multi-images-wrapper").find('input[type="file"]').slice(1).remove();
    }

    $("#edit-close, #edit-cancel").click(closeModal);

    // ------------- Submit Edit Form -------------
    $("#edit-property-form").submit(function(e){
        e.preventDefault();
        const propertyId = $("#edit-property-id").val();
        if(!propertyId) return window.pmShowToast("Property ID missing!", "error");

        const payload = {
            _method: "PUT",
            category: $("#edit-category").val(),
            location: $("#edit-location").val(),
            price: Number($("#edit-price").val()),
            status: $("#edit-status").val(),
            user_id: $("#edit-user-id").val() || undefined,
            description: $("#edit-description").val(),
            installment_years: parseInt($("#edit-installment-years").val()) || undefined,
            transaction_type: $("#edit-transaction-type").val(),
        };

        const formData = new FormData();
        Object.entries(payload).forEach(([k,v]) => { if(v!==undefined) formData.append(k,v); });

        // Single image
        const img = $("#edit-image")[0];
        if(img?.files.length) formData.append("image", img.files[0]);

        // Multiple images
        $("#edit-multi-images-wrapper input[type=file]").each((i,input)=>{
            if(input.files.length) formData.append("multiple_images[]", input.files[0]);
        });

        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop("disabled", true).text("Saving...");

        $.ajax({
            url: `/properties/${propertyId}`,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                submitBtn.prop("disabled", false).text(originalText);
                const updated = res.property || payload;

                const row = $(`#properties-list tbody tr`).filter(function(){ 
                    return $(this).find("td:first-child").text().trim() === propertyId;
                });

                if(row.length){
                    row.find("td:nth-child(2)").text(updated.category);
                    row.find("td:nth-child(3)").text(updated.location);
                    row.find("td:nth-child(4)").text(Number(updated.price).toLocaleString() + " EGP");
                    row.find("td:nth-child(5)").text(updated.status.charAt(0).toUpperCase() + updated.status.slice(1));
                    row.find("td:nth-child(6)").text(updated.user_id || "");
                    row.data("description", updated.description);
                    row.data("installment", updated.installment_years);
                    row.data("transaction", updated.transaction_type);
                }

                closeModal();
                window.pmShowToast("Property updated successfully!", "success");
            },
            error: function(xhr){
                submitBtn.prop("disabled", false).text(originalText);
                let message = "Error updating property";
                try{
                    const err = xhr.responseJSON || JSON.parse(xhr.responseText);
                    if(err.errors) message += ": "+Object.values(err.errors).flat().join(", ");
                    else if(err.message) message += ": "+err.message;
                } catch { message += ": " + xhr.statusText; }
                window.pmShowToast(message, "error");
            }
        });
    });

});
